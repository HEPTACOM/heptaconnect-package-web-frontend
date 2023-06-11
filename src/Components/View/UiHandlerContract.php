<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\View;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Notification\Notification;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Notification\NotificationBag;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\AbstractPage;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\PageRendererInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerStackInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class UiHandlerContract extends HttpHandlerContract
{
    private ?ContainerInterface $container = null;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        HttpHandleContextInterface $context,
        HttpHandlerStackInterface $stack
    ): ResponseInterface {
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

    protected function render(AbstractPage $page, ?ServerRequestInterface $request = null): ResponseInterface
    {
        /** @var PageRendererInterface $pageRenderer */
        $pageRenderer = $this->container->get(PageRendererInterface::class);

        return $pageRenderer->render($page, $request);
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
        $notifications = $this->container->get(NotificationBag::class);

        return $notifications;
    }
}
