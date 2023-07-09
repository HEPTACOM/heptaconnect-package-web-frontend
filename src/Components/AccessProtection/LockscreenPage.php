<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\AbstractPage;

final class LockscreenPage extends AbstractPage
{
    public function __construct(
        private string $username,
        private bool $error = false,
    ) {
    }

    public function getTemplate(): string
    {
        return '@WebFrontendPackage/ui/page/lockscreen/index.html.twig';
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function isError(): bool
    {
        return $this->error;
    }
}
