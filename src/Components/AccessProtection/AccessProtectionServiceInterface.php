<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection;

use Psr\Http\Server\MiddlewareInterface;

interface AccessProtectionServiceInterface extends MiddlewareInterface
{
    public function generateLoginUrl(): string;
}
