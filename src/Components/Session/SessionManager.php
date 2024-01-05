<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Session;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionManagerInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerUrlProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;

final class SessionManager implements SessionManagerInterface
{
    public const REQUEST_ATTRIBUTE_SESSION = 'session';

    public const RESPONSE_HEADER_SESSION = 'X-Session-ID';

    private \DateInterval $sessionLifetime;

    public function __construct(
        private CacheInterface $sessionCache,
        private HttpHandlerUrlProviderInterface $urlProvider,
        private string $cookieName,
        private string $cachePrefix,
        string $sessionLifetime
    ) {
        $this->sessionLifetime = new \DateInterval($sessionLifetime);
    }

    public function hasSession(ServerRequestInterface $request): bool
    {
        $sessionId = $this->getSessionIdFromRequest($request);

        return $sessionId !== null && $this->exists($sessionId);
    }

    public function getSessionFromRequest(ServerRequestInterface $request): ?SessionInterface
    {
        $session = $request->getAttribute(self::REQUEST_ATTRIBUTE_SESSION);

        if ($session instanceof SessionInterface) {
            return $session;
        }

        $sessionId = $this->getSessionIdFromRequest($request);

        if ($sessionId === null || !$this->exists($sessionId)) {
            return null;
        }

        return $this->createSessionFromId($sessionId);
    }

    public function createSession(ServerRequestInterface $request): SessionInterface
    {
        $sessionId = $this->getSessionIdFromRequest($request);

        if ($sessionId !== null && $this->exists($sessionId)) {
            throw new \UnexpectedValueException('Session is already started', 1688259000);
        }

        $sessionId = \bin2hex(\random_bytes(32));

        return $this->createSessionFromId($sessionId);
    }

    public function destroySession(ServerRequestInterface $request): void
    {
        $sessionId = $this->getSessionIdFromRequest($request);

        if ($sessionId === null || !$this->exists($sessionId)) {
            return;
        }

        $this->getSessionFromRequest($request)?->clear();

        $this->sessionCache->delete($this->getInternalKey($sessionId));
    }

    public function getSessionFromId(string $sessionId): ?SessionInterface
    {
        if (!$this->exists($sessionId)) {
            return null;
        }

        return $this->createSessionFromId($sessionId);
    }

    public function alterResponse(SessionInterface $session, ResponseInterface $response): ResponseInterface
    {
        $referenceTime = new \DateTimeImmutable();
        $maxAge = $this->getSessionMaxAge($referenceTime, $this->sessionLifetime);
        $expiration = $this->getSessionExpiration($referenceTime, $this->sessionLifetime);

        $setCookieHeader = \sprintf(
            $this->cookieName . '=%s; HttpOnly; Max-Age=%s; Expires=%s; Path=%s',
            $session->getId(),
            $maxAge,
            $expiration,
            \rtrim($this->urlProvider->resolve('/')->getPath(), '/')
        );

        $this->setStorage($session->getId(), $session->all());

        return $response
            ->withHeader('Set-Cookie', $setCookieHeader)
            ->withHeader(self::RESPONSE_HEADER_SESSION, $session->getId());
    }

    public function saveSession(SessionInterface $session): void
    {
        $this->setStorage($session->getId(), $session->all());
    }

    private function getInternalKey(string $sessionId): string
    {
        return $this->cachePrefix . $sessionId;
    }

    private function exists(string $sessionId): bool
    {
        return $this->sessionCache->has($this->getInternalKey($sessionId));
    }

    private function createSessionFromId(string $sessionId): Session
    {
        $storage = $this->sessionCache->get($this->getInternalKey($sessionId));
        $sessionValues = null;

        if (\is_array($storage)) {
            $sessionValues = $storage;
        }

        if ($sessionValues === null) {
            $sessionValues = [];
            $this->setStorage($sessionId, $sessionValues);
        }

        return new Session($sessionId, $sessionValues);
    }

    private function getSessionIdFromRequest(ServerRequestInterface $request): ?string
    {
        $session = $request->getAttribute(self::REQUEST_ATTRIBUTE_SESSION);

        if ($session instanceof SessionInterface) {
            $sessionId = $session->getId();
        } else {
            $sessionId = $request->getCookieParams()[$this->cookieName] ?? null;
        }

        return $sessionId;
    }

    private function getSessionMaxAge(\DateTimeImmutable $reference, \DateInterval $sessionLifetime): int
    {
        return (int) $reference->add($sessionLifetime)->format('U') - (int) $reference->format('U');
    }

    private function getSessionExpiration(\DateTimeImmutable $reference, \DateInterval $sessionLifetime): string
    {
        $timestamp = (int) $reference->add($sessionLifetime)->format('U');

        return \gmdate('D, d-M-Y H:i:s T', $timestamp);
    }

    private function setStorage(string $sessionId, array $storage): bool
    {
        return $this->sessionCache->set(
            $this->getInternalKey($sessionId),
            $storage,
            $this->sessionLifetime
        );
    }
}
