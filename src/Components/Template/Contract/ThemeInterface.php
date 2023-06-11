<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Utility\ThemePackageTrait;

/**
 * Describe a theme package.
 *
 * Use trait directly or as reference implementation @see ThemePackageTrait
 */
interface ThemeInterface
{
    /**
     * Get the absolute path to a template root directory.
     * These templates need to be render-able by the renderer created by @see TwigEnvironmentFactoryInterface
     */
    public function getThemeTemplatesPath(): string;

    /**
     * Get the absolute path to a asset root directory.
     * The assets are meant to be served to web browsers.
     */
    public function getThemeAssetPath(): string;

    /**
     * Get name of the theme package.
     * The name can be used to locate the origin of template files.
     */
    public function getThemeName(): string;
}
