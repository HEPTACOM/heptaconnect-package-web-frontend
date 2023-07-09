<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\DefaultPage\DefaultUiHandler;
use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\AbstractFeature;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PageFeature extends AbstractFeature
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = \array_replace_recursive([], ...$configs);
        $enabled = $this->isConfigEnabled($container, $config);
        $defaultPageEnabled = (bool) ($config['default_page_enabled'] ?? true);
        $defaultPagePath = (string) $config['default_page_path'];

        $container->setParameter($this->getAlias() . '.enabled', $enabled);

        if (!$enabled) {
            return;
        }

        $container->setParameter($this->getAlias() . '.default_page_enabled', $defaultPageEnabled);
        $container->setParameter($this->getAlias() . '.default_page_path', $defaultPagePath);

        $this->loadServicesXml($container);

        if (!$defaultPageEnabled) {
            $container->removeDefinition(DefaultUiHandler::class);
        }
    }

    protected function getDefaultConfiguration(): array
    {
        return [
            'enabled' => true,
            'default_page_enabled' => null, // true | false
            'default_page_path' => 'ui', // 'string'
        ];
    }

    protected function getPath(): string
    {
        return __DIR__;
    }
}
