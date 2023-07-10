<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Session;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SessionMiddleware implements MiddlewareInterface
{
    /**
     * @var array<ServerRequestInterface>
     */
    private array $requestStack = [];

    public function __construct(
        private SessionManagerInterface $sessionManager,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestStack = $this->requestStack;
        \array_unshift($requestStack, $request);
        $session = null;

        do {
            $currentRequest = \array_shift($requestStack);

            if (!$currentRequest instanceof ServerRequestInterface) {
                break;
            }

            $session = $this->sessionManager->getSessionFromRequest($currentRequest);
        } while (!$session instanceof SessionInterface);

        if (!$session instanceof SessionInterface) {
            return $handler->handle($request);
        }

        $request = $request->withAttribute(SessionManager::REQUEST_ATTRIBUTE_SESSION, $session);
        \array_unshift($this->requestStack, $request);

        try {
            $response = $handler->handle($request);

            // if someone else set a session, we would likely overwrite it with an old one, so we better skip
            if (!$response->hasHeader(SessionManager::RESPONSE_HEADER_SESSION)) {
                return $this->sessionManager->alterResponse($session, $response);
            }

            return $response;
        } catch (\Throwable $throwable) {
            $this->sessionManager->saveSession($session);

            throw $throwable;
        } finally {
            \array_shift($this->requestStack);
        }
    }
}
