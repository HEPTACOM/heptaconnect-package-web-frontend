<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\Debug;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\TwigEnvironmentFactoryInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;

final class DebugTwigEnvironmentFactory implements TwigEnvironmentFactoryInterface
{
    public function __construct(
        private TwigEnvironmentFactoryInterface $decorated,
    ) {
    }

    public function factory(): Environment
    {
        $result = $this->decorated->factory();

        $result->enableDebug();
        $result->enableAutoReload();
        $result->addExtension(new DebugExtension());

        return $result;
    }
}
