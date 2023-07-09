<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\AbstractPage;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RemovePagesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $definitionId => $definition) {
            $class = $definition->getClass();

            if ($class !== null && \is_a($class, AbstractPage::class, true)) {
                $container->removeDefinition($definitionId);
            }
        }
    }
}
