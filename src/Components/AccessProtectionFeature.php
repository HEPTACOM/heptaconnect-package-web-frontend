<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components;

use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\AbstractFeature;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AccessProtectionFeature extends AbstractFeature
{
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

        $this->loadServicesXml($container);
    }

    protected function getDefaultConfiguration(): array
    {
        return [
            'after_login_page_path' => 'ui', // 'string'
            'after_logout_page_path' => 'ui', // 'string'
            'login_page_path' => '_access/lockscreen', // 'string'
            'login_path' => '_access/login', // 'string'
            'logout_path' => '_access/logout', // 'string'
        ];
    }

    protected function getPath(): string
    {
        return __DIR__;
    }
}
