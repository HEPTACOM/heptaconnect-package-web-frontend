<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Service working to work with session data attached to requests and responses.
 */
interface SessionManagerInterface
{
    /**
     * Checks the request for a valid session or session reference.
     */
    public function hasSession(ServerRequestInterface $request): bool;

    /**
     * Retrieves the session from the given request. When no session is found, null is returned.
     */
    public function getSessionFromRequest(ServerRequestInterface $request): ?SessionInterface;

    /**
     * Retrieves the session from a session id. When no session data is found, null is returned.
     */
    public function getSessionFromId(string $sessionId): ?SessionInterface;

    /**
     * Create a session for the given request.
     *
     * @throws \UnexpectedValueException if the request already has a session or session reference
     */
    public function createSession(ServerRequestInterface $request): ?SessionInterface;

    /**
     * Remove any given session and session reference from the storage.
     */
    public function destroySession(ServerRequestInterface $request): void;

    /**
     * Add session or session reference to the response so the client can reuse it to pass it back in a request.
     * It is ensured, that the session is stored.
     */
    public function alterResponse(SessionInterface $session, ResponseInterface $response): ResponseInterface;

    /**
     * Store the session.
     */
    public function saveSession(SessionInterface $session): void;
}
