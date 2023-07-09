<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class DebugFeature extends Extension implements PrependExtensionInterface
{
    public static function getName(): string
    {
        $class = self::class;
        $lastNamespaceSeparator = \mb_strrchr($class, '\\');

        if ($lastNamespaceSeparator !== false) {
            $class = \mb_substr($lastNamespaceSeparator, 1, -7);
        }

        return Container::underscore('WebFrontendTemplate' . $class);
    }

    public function getAlias()
    {
        return self::getName();
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig($this->getAlias(), [
            'enabled' => false,
            'html_error_renderer' => null, // true | false
        ]);
    }

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

        $containerConfigurationPath = __DIR__ . '/Debug/Resources/config';
        $xmlLoader = new XmlFileLoader($container, new FileLocator($containerConfigurationPath));

        $xmlLoader->load($containerConfigurationPath . '/services.xml');
    }
}
