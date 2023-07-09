<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\Contract\AuthorizationBackendInterface;
use Heptacom\HeptaConnect\Portal\Base\File\Filesystem\Contract\FilesystemInterface;

final class AuthorizationBackend implements AuthorizationBackendInterface
{
    public function __construct(
        private FilesystemInterface $filesystem
    ) {
    }

    public function createUser(string $username, string $password): void
    {
        $newUsername = \base64_encode($username);
        $newPassword = \password_hash($password, \PASSWORD_BCRYPT);

        $resource = $this->getResource();

        $currentPassword = $this->findPassword($resource, $newUsername);
        if (\is_string($currentPassword)) {
            throw new \Exception('Username already taken');
        }

        \fseek($resource, 0, \SEEK_END);
        \fwrite($resource, $newUsername . ':' . $newPassword . \PHP_EOL);
        \fclose($resource);
    }

    public function verify(string $username, string $password): bool
    {
        $username = \base64_encode($username);

        $resource = $this->getResource();

        $currentPassword = $this->findPassword($resource, $username);
        if (!\is_string($currentPassword)) {
            return false;
        }

        return \password_verify($password, $currentPassword);
    }

    public function listUsers(): iterable
    {
        $resource = $this->getResource();

        while (($line = \fgets($resource)) !== false) {
            $line = \trim($line);
            [$username] = \explode(':', $line, 2);
            $result = \base64_decode($username, true);

            if ($result === false) {
                continue;
            }

            yield $result;
        }
    }

    /**
     * @return resource
     */
    private function getResource()
    {
        $storagePath = $this->filesystem->toStoragePath('.htpasswd');

        if (!\file_exists($storagePath)) {
            \touch($storagePath);
        }

        $result = \fopen($storagePath, 'r+b');

        if ($result === false) {
            throw new \RuntimeException('Failed to open user directory file', 1688913000);
        }

        return $result;
    }

    /**
     * @param resource $resource
     */
    private function findPassword($resource, string $newUsername): ?string
    {
        while (($line = \fgets($resource)) !== false) {
            $line = \trim($line);
            [$currentUsername, $currentPassword] = \explode(':', $line, 2);

            if ($newUsername === $currentUsername) {
                return $currentPassword;
            }
        }

        return null;
    }
}
