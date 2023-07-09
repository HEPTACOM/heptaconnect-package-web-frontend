<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\Contract;

/**
 * Describes a storage for authorizing users.
 */
interface AuthorizationBackendInterface
{
    /**
     * Add a user to the storage.
     */
    public function createUser(string $username, string $password): void;

    /**
     * Verify, that the given user exists in the storage and can be authenticated with the given password.
     */
    public function verify(string $username, string $password): bool;

    /**
     * Returns all available users.
     *
     * @return iterable<string>
     */
    public function listUsers(): iterable;
}
