<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract;

use Twig\Environment;

interface TwigEnvironmentFactoryInterface
{
    /**
     * Create a new twig environment to render web frontends.
     */
    public function createTwigEnvironment(): Environment;
}
