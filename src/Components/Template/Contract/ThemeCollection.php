<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract;

use Heptacom\HeptaConnect\Dataset\Base\ScalarCollection\StringCollection;
use Heptacom\HeptaConnect\Dataset\Base\Support\AbstractObjectCollection;

/**
 * @extends AbstractObjectCollection<ThemeInterface>
 */
final class ThemeCollection extends AbstractObjectCollection
{
    private bool $isInRenderOrder = false;

    public function getRenderOrder(): ThemeCollection
    {
        $result = new ThemeCollection($this);

        if (!$this->isInRenderOrder) {
            $result->reverse();
        }

        $result->isInRenderOrder = true;

        return $result;
    }

    public function getNames(): StringCollection
    {
        return new StringCollection($this->map(static fn (ThemeInterface $theme): string => $theme->getThemeName()));
    }

    public function getTemplatePath(string $themeName): ?string
    {
        foreach ($this as $theme) {
            if ($theme->getThemeName() === $themeName) {
                return $theme->getThemeTemplatesPath();
            }
        }

        return null;
    }

    public function getAssetPath(string $themeName): ?string
    {
        foreach ($this as $theme) {
            if ($theme->getThemeName() === $themeName) {
                return $theme->getThemeAssetPath();
            }
        }

        return null;
    }

    protected function getT(): string
    {
        return ThemeInterface::class;
    }
}
