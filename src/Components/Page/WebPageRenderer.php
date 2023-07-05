<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\WebPageRendererInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\WebPageTwigEnvironmentFactoryInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class WebPageRenderer implements WebPageRendererInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private WebPageTwigEnvironmentFactoryInterface $webPageTwigEnvironmentFactory
    ) {
    }

    public function render(
        AbstractPage $page,
        ServerRequestInterface $request,
        HttpHandleContextInterface $context
    ): ResponseInterface {
        $twig = $this->webPageTwigEnvironmentFactory->createTwigEnvironment($request, $context);

        $nonce = \bin2hex(\random_bytes(16));

        $twig->addGlobal('nonce', $nonce);

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
}
