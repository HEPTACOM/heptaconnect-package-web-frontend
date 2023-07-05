<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Notification\NotificationBag;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\WebPageTwigEnvironmentFactoryInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\TwigEnvironmentFactoryInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

final class WebPageTwigEnvironmentFactory implements WebPageTwigEnvironmentFactoryInterface
{
    public function __construct(
        private NotificationBag $notifications,
        private TwigEnvironmentFactoryInterface $twigEnvironmentFactory,
    ) {
    }

    public function createTwigEnvironment(
        ServerRequestInterface $request,
        HttpHandleContextInterface $context
    ): Environment {
        $twig = $this->twigEnvironmentFactory->createTwigEnvironment();

        $twig->addGlobal('notifications', $this->notifications);
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

    private function getCurrentUri(ServerRequestInterface $request): ?string
    {
        return (string) $request->getUri();
    }
}
