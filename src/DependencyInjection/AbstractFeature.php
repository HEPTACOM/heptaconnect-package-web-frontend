<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

abstract class AbstractFeature extends Extension implements PrependExtensionInterface
{
    public static function getName(): string
    {
        return Container::underscore(static::getFeaturePrefix() . static::getShortClassNameWithoutSuffix());
    }

    public function getAlias()
    {
        return static::getName();
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig($this->getAlias(), $this->getDefaultConfiguration());
    }

    abstract protected function getPath(): string;

    abstract protected function getDefaultConfiguration(): array;

    protected static function getFeaturePrefix(): string
    {
        return 'WebFrontend';
    }

    protected function loadServicesXml(ContainerBuilder $container): void
    {
        $containerConfigurationPath = $this->getPath() . '/' . static::getShortClassNameWithoutSuffix() . '/Resources/config';
        $xmlLoader = new XmlFileLoader($container, new FileLocator($containerConfigurationPath));

        $xmlLoader->load($containerConfigurationPath . '/services.xml');
    }

    protected final static function getShortClassNameWithoutSuffix(): string
    {
        $class = static::class;
        $lastNamespaceSeparator = \mb_strrchr($class, '\\');

        if ($lastNamespaceSeparator !== false) {
            $class = \mb_substr($lastNamespaceSeparator, 1, -7);
        }

        return $class;
    }
}
