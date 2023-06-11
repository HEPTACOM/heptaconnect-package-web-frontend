<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract;

interface ThemeInterface
{
    public function getThemeTemplatesPath(): string;

    public function getThemeAssetPath(): string;

    public function getThemeName(): string;
}
