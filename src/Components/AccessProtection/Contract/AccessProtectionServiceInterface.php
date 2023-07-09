<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\Contract;

interface AccessProtectionServiceInterface
{
    /**
     * Generate a URL, that provides a successful login in a web browser.
     */
    public function generateLoginUrl(): string;
}
