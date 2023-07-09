<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class AccessProtectionFeature extends Extension implements PrependExtensionInterface
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
            'after_login_page_path' => 'ui', // 'string'
            'after_logout_page_path' => 'ui', // 'string'
            'login_page_path' => '_access/lockscreen', // 'string'
            'login_path' => '_access/login', // 'string'
            'logout_path' => '_access/logout', // 'string'
        ]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = \array_replace_recursive([], ...$configs);
        $loginPath = (string) $config['login_path'];
        $loginPagePath = (string) $config['login_page_path'];
        $logoutPath = (string) $config['logout_path'];
        $afterLoginPagePath = (string) $config['after_login_page_path'];
        $afterLogoutPagePath = (string) $config['after_logout_page_path'];

        $container->setParameter($this->getAlias() . '.login_path', $loginPath);
        $container->setParameter($this->getAlias() . '.login_page_path', $loginPagePath);
        $container->setParameter($this->getAlias() . '.logout_path', $logoutPath);
        $container->setParameter($this->getAlias() . '.after_login_page_path', $afterLoginPagePath);
        $container->setParameter($this->getAlias() . '.after_logout_page_path', $afterLogoutPagePath);

        $containerConfigurationPath = __DIR__ . '/AccessProtection/Resources/config';
        $xmlLoader = new XmlFileLoader($container, new FileLocator($containerConfigurationPath));

        $xmlLoader->load($containerConfigurationPath . '/services.xml');
    }
}
