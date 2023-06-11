<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Twig\Extra\Intl\IntlExtension;

final class RegisterSuggestedTwigExtensionsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (\class_exists(IntlExtension::class)) {
            $definition = new Definition(IntlExtension::class);
            $definition->addTag('twig.extension');

            $container->setDefinition(IntlExtension::class, $definition);
        }
    }
}
