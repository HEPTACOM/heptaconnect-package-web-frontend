<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\ThemeInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\WebFrontendPackage;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalExtensionContract;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TemplateTagCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $definition) {
            $class = $definition->getClass();

            if (
                !\is_string($class)
                || !\is_subclass_of($class, ThemeInterface::class)
                || $definition->hasTag('web_frontend.theme')
            ) {
                continue;
            }

            $definition->addTag('web_frontend.theme', [
                'priority' => $this->getPriority($class),
            ]);
        }
    }

    private function getPriority(string $class): int
    {
        if (\is_subclass_of($class, PortalExtensionContract::class)) {
            return -2000;
        }

        if (\is_subclass_of($class, PortalContract::class)) {
            return -1000;
        }

        if ($class === WebFrontendPackage::class) {
            return 1000;
        }

        return 0;
    }
}
