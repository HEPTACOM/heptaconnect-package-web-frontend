<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Session;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SessionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private SessionManagerInterface $sessionManager,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $hasSession = $this->sessionManager->hasSession($request);

        if ($hasSession) {
            $session = $this->sessionManager->getSession($request);

            $request = $request->withAttribute(
                SessionManager::REQUEST_ATTRIBUTE_SESSION,
                $session
            );
        }

        $response = $handler->handle($request);

        if ($hasSession) {
            $response = $this->sessionManager->addResponseHeader($session, $response);
        }

        return $response;
    }
}
