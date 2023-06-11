<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template;

interface ThemeInterface
{
    public function getTemplatesPath(): string;

    public function getAssetPath(): string;

    public function getName(): string;
}
