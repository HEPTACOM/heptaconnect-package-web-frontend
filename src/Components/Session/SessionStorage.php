<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Session;

use Psr\SimpleCache\CacheInterface;

final class SessionStorage
{
    private const STORAGE_PREFIX = 'session.storage.';

    public function __construct(
        private string $sessionId,
        private CacheInterface $sessionCache,
    ) {
    }

    public static function exists(string $sessionId, CacheInterface $portalStorage): bool
    {
        return $portalStorage->has(self::getInternalKey($sessionId));
    }

    public function get(string $key): null|bool|int|float|string|array
    {
        $storage = $this->getStorage();

        return $storage[$key] ?? null;
    }

    public function set(string $key, null|bool|int|float|string|array $value): bool
    {
        $storage = $this->getStorage();
        $storage[$key] = $value;

        return $this->setStorage($storage);
    }

    public function delete(string $key): bool
    {
        $storage = $this->getStorage();
        unset($storage[$key]);

        return $this->setStorage($storage);
    }

    public function clear(): bool
    {
        return $this->sessionCache->delete(self::getInternalKey($this->sessionId));
    }

    public function has(string $key): bool
    {
        $storage = $this->getStorage();

        return \array_key_exists($key, $storage);
    }

    public function touch(): void
    {
        $this->setStorage($this->getStorage());
    }

    private static function getInternalKey(string $key): string
    {
        return self::STORAGE_PREFIX . $key;
    }

    private function getStorage(): array
    {
        $storage = $this->sessionCache->get(
            self::getInternalKey($this->sessionId)
        );

        if (\is_array($storage)) {
            return $storage;
        }

        return [];
    }

    private function setStorage(array $storage): bool
    {
        return $this->sessionCache->set(
            self::getInternalKey($this->sessionId),
            $storage,
        );
    }
}
