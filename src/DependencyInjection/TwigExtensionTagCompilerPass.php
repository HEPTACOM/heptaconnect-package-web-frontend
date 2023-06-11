<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Twig\Extension\ExtensionInterface;

final class TwigExtensionTagCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $definition) {
            $class = $definition->getClass();

            if (
                !\is_string($class)
                || !\is_subclass_of($class, ExtensionInterface::class)
                || $definition->hasTag('twig.extension')
            ) {
                continue;
            }

            $definition->addTag('twig.extension');
        }
    }
}
