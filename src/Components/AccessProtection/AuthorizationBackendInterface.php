<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection;

interface AuthorizationBackendInterface
{
    public function createUser(string $username, string $password): void;

    public function verify(string $username, string $password): bool;

    /**
     * @return iterable<string>
     */
    public function listUsers(): iterable;
}
