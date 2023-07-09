<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\TwigEnvironmentFactoryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ProvideContainerParameterForTwigEnvironmentCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $twigFactory = $container->getDefinition(TwigEnvironmentFactoryInterface::class);
        $parameters = [];

        foreach ($container->getParameterBag()->all() as $key => $value) {
            if (\str_starts_with($key, 'web_frontend_')) {
                $parameters[$key] = $value;
            }
        }

        $unrolled = [];

        foreach ($parameters as $key => $value) {
            $keyParts = \explode('.', $key);
            $pointer = &$unrolled;

            foreach ($keyParts as $keyPart) {
                if (!isset($pointer[$keyPart])) {
                    $pointer[$keyPart] = [];
                }

                $pointer = &$pointer[$keyPart];
            }

            $pointer = $value;
        }

        $twigFactory->setArgument('$containerParameter', $unrolled);
    }
}
