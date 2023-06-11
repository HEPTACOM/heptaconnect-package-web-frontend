<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template;

trait ThemePackageTrait
{
    public function getThemeTemplatesPath(): string
    {
        return \implode(\DIRECTORY_SEPARATOR, [
            $this->getPath(),
            'Resources',
            'views',
        ]);
    }

    public function getThemeAssetPath(): string
    {
        return \implode(\DIRECTORY_SEPARATOR, [
            $this->getPath(),
            'Resources',
            'public',
        ]);
    }

    public function getThemeName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}
