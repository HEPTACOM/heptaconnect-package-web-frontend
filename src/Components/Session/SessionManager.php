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

    private const COOKIE_NAME_SESSION_ID = 'HC_SESSION_ID';

    private const SESSION_LIFETIME = 'P30D';

    private const STORAGE_PREFIX = 'session.storage.';

    public function __construct(
        private CacheInterface $sessionCache,
        private HttpHandlerUrlProviderInterface $urlProvider
    ) {
    }

    public function hasSession(ServerRequestInterface $request): bool
    {
        return $this->exists($this->getSessionIdFromRequest($request));
    }

    public function getSessionFromRequest(ServerRequestInterface $request): ?SessionInterface
    {
        $sessionId = $this->getSessionIdFromRequest($request);

        if (!$this->exists($sessionId)) {
            return null;
        }

        return $this->createSessionFromId($sessionId);
    }

    public function createSession(ServerRequestInterface $request): ?SessionInterface
    {
        $sessionId = $this->getSessionIdFromRequest($request);

        if ($this->exists($sessionId)) {
            throw new \Exception('Session is already started');
        }

        $sessionId = \bin2hex(\random_bytes(32));

        return $this->createSessionFromId($sessionId);
    }

    public function destroySession(ServerRequestInterface $request): void
    {
        $sessionId = $this->getSessionIdFromRequest($request);

        if (!$this->exists($sessionId)) {
            return;
        }

        $this->sessionCache->delete(self::getInternalKey($sessionId));
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
        $sessionLifetime = new \DateInterval(self::SESSION_LIFETIME);
        $maxAge = $this->getSessionMaxAge($referenceTime, $sessionLifetime);
        $expiration = $this->getSessionExpiration($referenceTime, $sessionLifetime);

        $setCookieHeader = \sprintf(
            self::COOKIE_NAME_SESSION_ID . '=%s; HttpOnly; Max-Age=%s; Expires=%s; Path=%s',
            $session->getId(),
            $maxAge,
            $expiration,
            $this->urlProvider->resolve('')->getPath()
        );

        $this->setStorage($session->getId(), $session->all());

        return $response
            ->withHeader('Set-Cookie', $setCookieHeader)
            ->withHeader('X-Session-ID', $session->getId());
    }

    public function saveSession(SessionInterface $session): void
    {
        $this->setStorage($session->getId(), $session->all());
    }

    private static function getInternalKey(string $sessionId): string
    {
        return self::STORAGE_PREFIX . $sessionId;
    }

    /**
     * @phpstan-assert-if-true string $sessionId
     */
    private function exists(?string $sessionId): bool
    {
        if ($sessionId === null) {
            return false;
        }

        return $this->sessionCache->has(self::getInternalKey($sessionId));
    }

    private function createSessionFromId(string $sessionId): Session
    {
        $storage = $this->sessionCache->get(self::getInternalKey($sessionId));
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
            $sessionId = $request->getCookieParams()[self::COOKIE_NAME_SESSION_ID] ?? null;
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
            self::getInternalKey($sessionId),
            $storage,
            new \DateInterval(self::SESSION_LIFETIME)
        );
    }
}
