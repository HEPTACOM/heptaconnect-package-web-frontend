<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection;

use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalStorageInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerUrlProviderInterface;

final class AccessProtectionService implements AccessProtectionServiceInterface
{
    private const STORAGE_PREFIX_AUTH_TOKEN = 'config.access-protection.auth_token.';

    private const QUERY_PARAMETER_AUTH_TOKEN = 'access_protection_token';

    private const AUTH_TOKEN_LIFETIME = 'PT5M';

    public function __construct(
        private string $afterLoginPagePath,
        private PortalStorageInterface $portalStorage,
        private HttpHandlerUrlProviderInterface $urlProvider,
    ) {
    }

    public function generateLoginUrl(): string
    {
        $username = \uniqid();
        $password = \bin2hex(\random_bytes(32));
        $hashedPassword = \password_hash($password, \PASSWORD_BCRYPT);

        $this->portalStorage->set(
            self::STORAGE_PREFIX_AUTH_TOKEN . $username,
            $hashedPassword,
            new \DateInterval(self::AUTH_TOKEN_LIFETIME)
        );

        $credentials = \base64_encode(\sprintf('%s:%s', $username, $password));

        $query = \http_build_query([
            self::QUERY_PARAMETER_AUTH_TOKEN => $credentials,
        ]);

        return (string) $this->urlProvider->resolve($this->afterLoginPagePath)->withQuery($query);
    }
}
