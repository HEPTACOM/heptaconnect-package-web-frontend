<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components;

use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\AbstractFeature;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SessionFeature extends AbstractFeature
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = \array_replace_recursive([], ...$configs);
        $enabled = $this->isConfigEnabled($container, $config);

        $container->setParameter($this->getAlias() . '.enabled', $enabled);

        if (!$enabled) {
            return;
        }

        $container->setParameter($this->getAlias() . '.session_lifetime', $config['session_lifetime']);
        $container->setParameter($this->getAlias() . '.cookie_name', $config['cookie_name']);
        $container->setParameter($this->getAlias() . '.cache_key_prefix', $config['cache_key_prefix']);

        $this->loadServicesXml($container);
    }

    protected function getDefaultConfiguration(): array
    {
        return [
            'enabled' => true,
            'session_lifetime' => 'P30D',
            'cookie_name' => 'HC_SESSION_ID',
            'cache_key_prefix' => 'session.storage.',
        ];
    }

    protected function getPath(): string
    {
        return __DIR__;
    }
}
