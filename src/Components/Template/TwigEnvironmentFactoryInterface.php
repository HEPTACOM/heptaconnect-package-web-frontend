<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template;

use Twig\Environment;

interface TwigEnvironmentFactoryInterface
{
    public function factory(): Environment;
}
