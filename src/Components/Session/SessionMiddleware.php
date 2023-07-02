<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Session;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionManagerInterface;
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
        $session = $this->sessionManager->getSessionFromRequest($request);

        if ($session === null) {
            return $handler->handle($request);
        }

        $request = $request->withAttribute(SessionManager::REQUEST_ATTRIBUTE_SESSION, $session);

        try {
            $response = $handler->handle($request);

            return $this->sessionManager->alterResponse($session, $response);
        } catch (\Throwable $throwable) {
            $this->sessionManager->saveSession($session);

            throw $throwable;
        }
    }
}
