<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\UiHandlerContract;
use Heptacom\HeptaConnect\Package\WebFrontend\WebFrontendPackage;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ControllerPreparationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $defId => $definition) {
            $class = $definition->getClass();

            if ($class === null) {
                continue;
            }

            if (\is_subclass_of($class, UiHandlerContract::class)) {
                $definition->addTag('web_frontend.ui_handler');
            }

            if (\str_starts_with($defId, 'Heptacom\\HeptaConnect\\Package\\WebFrontend') && \is_subclass_of($class, HttpHandlerContract::class)) {
                $definition->addTag('heptaconnect.flow_component.web_http_handler_source', [
                    'source' => WebFrontendPackage::class,
                ]);
            }
        }
    }
}
