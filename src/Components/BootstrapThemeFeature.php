<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components;

use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\AbstractFeature;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class BootstrapThemeFeature extends AbstractFeature
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = \array_replace_recursive([], ...$configs);
        $enabled = $this->isConfigEnabled($container, $config);

        $container->setParameter($this->getAlias() . '.enabled', $enabled);

        if (!$enabled) {
            return;
        }

        $this->loadServicesXml($container);
    }

    protected function getDefaultConfiguration(): array
    {
        return [
            'enabled' => true,
        ];
    }

    protected function getPath(): string
    {
        return __DIR__;
    }
}
