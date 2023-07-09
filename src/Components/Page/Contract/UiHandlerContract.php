<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Notification\Notification;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Notification\NotificationBag;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionManagerInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerStackInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Describes an HTTP handler, that is handling human requests and therefore has to behave well for web browsers.
 * It supports basic ACL using @see isProtected
 */
abstract class UiHandlerContract extends HttpHandlerContract
{
    private ServerRequestInterface $handlingRequest;

    private HttpHandleContextInterface $handlingContext;

    public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        HttpHandleContextInterface $context,
        HttpHandlerStackInterface $stack
    ): ResponseInterface {
        $this->handlingRequest = $request;
        $this->handlingContext = $context;

        try {
            return parent::handle(
                $request,
                $response,
                $context,
                $stack
            );
        } finally {
            $this->getNotifications()->clear();
        }
    }

    /**
     * Returns a state, whether protection is needed.
     * If protection is needed, the AccessProtection feature can validate requests against this.
     */
    public function isProtected(ServerRequestInterface $request): bool
    {
        return true;
    }

    /**
     * Render the given page and return an HTML response.
     */
    protected function render(AbstractPage $page): ResponseInterface
    {
        /** @var WebPageRendererInterface $pageRenderer */
        $pageRenderer = $this->handlingContext->getContainer()->get(WebPageRendererInterface::class);

        return $pageRenderer->render($page, $this->handlingRequest, $this->handlingContext);
    }

    /**
     * Store a notification storage so it can be rendered later.
     */
    protected function notify(string $type, string $message): void
    {
        /** @var SessionManagerInterface $sessionManager */
        $sessionManager = $this->handlingContext->getContainer()->get(SessionManagerInterface::class);
        $session = $sessionManager->getSessionFromRequest($this->handlingRequest);

        if ($session !== null) {
            /** @var array $notifications */
            $notifications = $session->get('notifications') ?? [];
            $notifications[] = new Notification($type, $message);
            $session->set('notifications', $notifications);
        } else {
            $this->getNotifications()->push([
                new Notification($type, $message),
            ]);
        }
    }

    private function getNotifications(): NotificationBag
    {
        /** @var NotificationBag $notifications */
        $notifications = $this->handlingContext->getContainer()->get(NotificationBag::class);

        return $notifications;
    }
}
