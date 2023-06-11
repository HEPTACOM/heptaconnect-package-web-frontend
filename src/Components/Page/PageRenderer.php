<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Notification\NotificationBag;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\TwigEnvironmentFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class PageRenderer implements PageRendererInterface
{
    public function __construct(
        private NotificationBag $notifications,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private TwigEnvironmentFactoryInterface $twigEnvironmentFactory,
    ) {
    }

    public function render(AbstractPage $page, ?ServerRequestInterface $request = null): ResponseInterface
    {
        $twig = $this->twigEnvironmentFactory->factory();

        $nonce = \bin2hex(\random_bytes(16));

        $twig->addGlobal('nonce', $nonce);
        $twig->addGlobal('notifications', $this->notifications);
        $twig->addGlobal('currentPage', $this->getCurrentPage($request));
        $twig->addGlobal('currentUri', $this->getCurrentUri($request));
        $twig->addGlobal('colorScheme', $this->getColorScheme($request));

        $renderedBody = $twig->render($page->getTemplate(), ['page' => $page]);

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Security-Policy', \sprintf("script-src 'nonce-%s'", $nonce))
            ->withHeader('Content-Type', 'text/html; charset=utf-8')
            ->withHeader('Accept-CH', 'Sec-CH-Prefers-Color-Scheme')
            ->withHeader('Vary', 'Sec-CH-Prefers-Color-Scheme')
            ->withHeader('Critical-CH', 'Sec-CH-Prefers-Color-Scheme')
            ->withHeader('X-Robots-Tag', 'none')
            ->withBody($this->streamFactory->createStream($renderedBody))
        ;

        return $response;
    }

    private function getColorScheme(?ServerRequestInterface $request): string
    {
        if ($request instanceof ServerRequestInterface) {
            $preferredColorScheme = \strtolower($request->getHeaderLine('Sec-CH-Prefers-Color-Scheme'));

            if (\in_array($preferredColorScheme, ['light', 'dark'], true)) {
                return $preferredColorScheme;
            }
        }

        return 'light';
    }

    private function getCurrentPage(?ServerRequestInterface $request): ?string
    {
        if ($request instanceof ServerRequestInterface) {
            return $request->getUri()->getPath();
        }

        return null;
    }

    private function getCurrentUri(?ServerRequestInterface $request): ?string
    {
        if ($request instanceof ServerRequestInterface) {
            return (string) $request->getUri();
        }

        return null;
    }
}
