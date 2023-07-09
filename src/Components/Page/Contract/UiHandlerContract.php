<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Notification\Notification;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Notification\NotificationBag;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerStackInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

    public function isProtected(ServerRequestInterface $request): bool
    {
        return true;
    }

    protected function render(AbstractPage $page): ResponseInterface
    {
        /** @var WebPageRendererInterface $pageRenderer */
        $pageRenderer = $this->handlingContext->getContainer()->get(WebPageRendererInterface::class);

        return $pageRenderer->render($page, $this->handlingRequest, $this->handlingContext);
    }

    protected function notify(string $type, string $message): void
    {
        $this->getNotifications()->push([
            new Notification($type, $message),
        ]);
    }

    private function getNotifications(): NotificationBag
    {
        /** @var NotificationBag $notifications */
        $notifications = $this->handlingContext->getContainer()->get(NotificationBag::class);

        return $notifications;
    }
}
