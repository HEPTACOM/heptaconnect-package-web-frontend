<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface SessionManagerInterface
{
    public function hasSession(ServerRequestInterface $request): bool;

    public function getSession(ServerRequestInterface $request): ?SessionInterface;

    public function createSession(ServerRequestInterface $request): ?SessionInterface;

    public function destroySession(ServerRequestInterface $request): void;

    public function restoreSession(string $sessionId): ?SessionInterface;

    public function addResponseHeader(
        SessionInterface $session,
        ResponseInterface $response,
    ): ResponseInterface;
}
