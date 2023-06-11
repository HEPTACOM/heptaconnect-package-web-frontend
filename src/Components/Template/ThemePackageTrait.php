<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template;

trait ThemePackageTrait
{
    public function getTemplatesPath(): string
    {
        return \implode(\DIRECTORY_SEPARATOR, [
            $this->getPath(),
            'Resources',
            'views',
        ]);
    }

    public function getAssetPath(): string
    {
        return \implode(\DIRECTORY_SEPARATOR, [
            $this->getPath(),
            'Resources',
            'public',
        ]);
    }

    public function getName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}
