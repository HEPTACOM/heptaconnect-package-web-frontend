<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SessionFeature extends Extension implements PrependExtensionInterface
{
    public static function getName(): string
    {
        $classBaseName = substr(strrchr(self::class, '\\'), 1, -7);

        return Container::underscore('WebFrontendTemplate' . $classBaseName);
    }

    public function getAlias()
    {
        return self::getName();
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig($this->getAlias(), [
            'enabled' => true,
        ]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = \array_replace_recursive([], ...$configs);
        $enabled = $this->isConfigEnabled($container, $config);

        $container->setParameter($this->getAlias() . '.enabled', $enabled);

        if (!$enabled) {
            return;
        }

        $containerConfigurationPath = __DIR__ . '/Session/Resources/config';
        $xmlLoader = new XmlFileLoader($container, new FileLocator($containerConfigurationPath));

        $xmlLoader->load($containerConfigurationPath . '/services.xml');
    }
}
