<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\ErrorHandler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;

final class HttpErrorHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $exception) {
            // TODO: add config option to toggle debug mode
            $exception = (new HtmlErrorRenderer(true))->render($exception);

            $response = $this->responseFactory->createResponse($exception->getStatusCode());
            $response = $response->withBody($this->streamFactory->createStream($exception->getAsString()));

            foreach ($exception->getHeaders() as $headerName => $headerValue) {
                $response = $response->withHeader($headerName, $headerValue);
            }

            return $response;
        }
    }
}
