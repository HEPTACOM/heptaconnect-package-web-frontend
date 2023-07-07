<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\DefaultPage\DefaultUiHandler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class PageFeature extends Extension implements PrependExtensionInterface
{
    public static function getName(): string
    {
        $classBaseName = substr(strrchr(self::class, '\\'), 1, -7);

        return Container::underscore('WebFrontend' . $classBaseName);
    }

    public function getAlias()
    {
        return self::getName();
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig($this->getAlias(), [
            'enabled' => true,
            'default_page_enabled' => null, // true | false
            'default_page_path' => 'ui', // 'string'
            'after_login_page_path' => null, // 'string'
            'after_logout_page_path' => null, // 'string'
        ]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = \array_replace_recursive([], ...$configs);
        $enabled = $this->isConfigEnabled($container, $config);
        $defaultPageEnabled = (bool) ($config['default_page_enabled'] ?? true);
        $defaultPagePath = (string) $config['default_page_path'];
        $afterLoginPagePath = (string) ($config['after_login_page_path'] ?? $defaultPagePath);
        $afterLogoutPagePath = (string) ($config['after_logout_page_path'] ?? $defaultPagePath);

        $container->setParameter($this->getAlias() . '.enabled', $enabled);

        if (!$enabled) {
            return;
        }

        $container->setParameter($this->getAlias() . '.default_page_enabled', $defaultPageEnabled);
        $container->setParameter($this->getAlias() . '.default_page_path', $defaultPagePath);
        $container->setParameter($this->getAlias() . '.after_login_page_path', $afterLoginPagePath);
        $container->setParameter($this->getAlias() . '.after_logout_page_path', $afterLogoutPagePath);

        $containerConfigurationPath = __DIR__ . '/Page/Resources/config';
        $xmlLoader = new XmlFileLoader($container, new FileLocator($containerConfigurationPath));

        $xmlLoader->load($containerConfigurationPath . '/services.xml');

        if (!$defaultPageEnabled) {
            $container->removeDefinition(DefaultUiHandler::class);
        }
    }
}
