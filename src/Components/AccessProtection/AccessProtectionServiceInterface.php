<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection;

interface AccessProtectionServiceInterface
{
    public function generateLoginUrl(): string;
}
