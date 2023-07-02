<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract;

/**
 * Resemble session data in a mutable manner.
 */
interface SessionInterface
{
    /**
     * Get the session id, that is used to identify publicly.
     */
    public function getId(): string;

    /**
     * Return all session values.
     */
    public function all(): array;

    /**
     * Return a single value by the given key.
     */
    public function get(string $key): null|bool|int|float|string|array;

    /**
     * Set a single value by the given key.
     */
    public function set(string $key, null|bool|int|float|string|array $value): void;

    /**
     * Remove an entry by the given key.
     */
    public function delete(string $key): void;

    /**
     * Remove all entries.
     */
    public function clear(): void;

    /**
     * Return whether an entry by the given key exists.
     */
    public function has(string $key): bool;
}
