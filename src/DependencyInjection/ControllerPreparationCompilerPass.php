<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\UiHandlerContract;
use Heptacom\HeptaConnect\Package\WebFrontend\WebFrontendPackage;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ControllerPreparationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $defId => $definition) {
            if (\is_subclass_of($definition->getClass(), UiHandlerContract::class)) {
                $definition->addMethodCall('setContainer', [
                    new Reference('service_container'),
                ]);

                $definition->addTag('web_frontend.ui_handler');
            }

            if (\str_starts_with($defId, 'Heptacom\\HeptaConnect\\Package\\WebFrontend') && \is_subclass_of($definition->getClass(), HttpHandlerContract::class)) {
                $definition->addTag('heptaconnect.flow_component.web_http_handler_source', [
                    'source' => WebFrontendPackage::class
                ]);
            }
        }
    }
}
