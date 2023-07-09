<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature;

use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\AbstractFeature;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DebugFeature extends AbstractFeature
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = \array_replace_recursive([], ...$configs);
        $enabled = $this->isConfigEnabled($container, $config);
        $htmlRenderer = $config['html_error_renderer'] ?? true;

        $container->setParameter($this->getAlias() . '.enabled', $enabled);
        $container->setParameter($this->getAlias() . '.html_error_renderer', $enabled && $htmlRenderer);

        if (!$enabled) {
            return;
        }

        $this->loadServicesXml($container);
    }

    protected function getDefaultConfiguration(): array
    {
        return [
            'enabled' => false,
            'html_error_renderer' => null, // true | false
        ];
    }

    protected static function getFeaturePrefix(): string
    {
        return parent::getFeaturePrefix() . 'Template';
    }

    protected function getPath(): string
    {
        return __DIR__;
    }
}
