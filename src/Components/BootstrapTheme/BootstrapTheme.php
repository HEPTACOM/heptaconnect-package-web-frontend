<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\BootstrapTheme;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\ThemeInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Utility\ThemePackageTrait;

final class BootstrapTheme implements ThemeInterface
{
    use ThemePackageTrait;

    public function getThemeName(): string
    {
        return 'WebFrontendPackage';
    }

    private function getPath(): string
    {
        return __DIR__;
    }
}
