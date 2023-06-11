<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\View\UiHandlerContract;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ControllerPreparationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $definition) {
            if (\is_subclass_of($definition->getClass(), UiHandlerContract::class)) {
                $definition->addMethodCall('setContainer', [
                    new Reference('service_container'),
                ]);

                $definition->addTag('web_frontend.ui_handler');
            }
        }
    }
}
