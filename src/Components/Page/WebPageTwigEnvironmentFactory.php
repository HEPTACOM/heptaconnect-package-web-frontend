<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Notification\Notification;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Notification\NotificationBag;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\WebPageTwigEnvironmentFactoryInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionManagerInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\TwigEnvironmentFactoryInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

final class WebPageTwigEnvironmentFactory implements WebPageTwigEnvironmentFactoryInterface
{
    public function __construct(
        private NotificationBag $notifications,
        private SessionManagerInterface $sessionManager,
        private TwigEnvironmentFactoryInterface $twigEnvironmentFactory,
    ) {
    }

    public function createTwigEnvironment(
        ServerRequestInterface $request,
        HttpHandleContextInterface $context
    ): Environment {
        $twig = $this->twigEnvironmentFactory->createTwigEnvironment();

        foreach ($request->getAttributes() as $attributeKey => $attribute) {
            if (\str_starts_with($attributeKey, 'twig.') && \mb_strlen($attributeKey) > 5) {
                $twigKey = \mb_substr($attributeKey, 5);
                $twigKey = \preg_replace_callback(
                    '/[^a-zA-Z0-9]+(.)/u',
                    static fn (array $matches): string => \strtoupper($matches[1]),
                    $twigKey
                );

                if (!\is_string($twigKey)) {
                    continue;
                }

                $twig->addGlobal($twigKey, $attribute);
            }
        }

        $notifications = new NotificationBag($this->notifications);
        $session = $this->sessionManager->getSessionFromRequest($request);

        if ($session !== null) {
            /** @var array<Notification> $sessionNotifications */
            $sessionNotifications = $session->get('notifications') ?? [];
            $notifications->push($sessionNotifications);

            $notifications = new class($session, $notifications) implements \IteratorAggregate {
                public function __construct(
                    private SessionInterface $session,
                    private \Traversable $notifications
                ) {
                }

                public function getIterator(): \Traversable
                {
                    // delete session notifications as soon as they are accessed so we can expect them to be delivered
                    $this->session->delete('notifications');

                    return $this->notifications;
                }
            };
        }

        $twig->addGlobal('notifications', $notifications);
        $twig->addGlobal('currentPath', $this->getCurrentPath($request));
        $twig->addGlobal('currentUri', $this->getCurrentUri($request));
        $twig->addGlobal('colorScheme', $this->getColorScheme($request));

        return $twig;
    }

    private function getColorScheme(ServerRequestInterface $request): string
    {
        $preferredColorScheme = \strtolower($request->getHeaderLine('Sec-CH-Prefers-Color-Scheme'));

        if (\in_array($preferredColorScheme, ['light', 'dark'], true)) {
            return $preferredColorScheme;
        }

        return 'light';
    }

    private function getCurrentPath(ServerRequestInterface $request): string
    {
        return $request->getUri()->getPath();
    }

    private function getCurrentUri(ServerRequestInterface $request): string
    {
        return (string) $request->getUri();
    }
}
