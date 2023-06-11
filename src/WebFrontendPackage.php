<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\ThemeInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\ThemePackageTrait;
use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\ControllerPreparationCompilerPass;
use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\RegisterSuggestedTwigExtensionsCompilerPass;
use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\RemovePagesCompilerPass;
use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\TemplateTagCompilerPass;
use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\TwigExtensionTagCompilerPass;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PackageContract;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class WebFrontendPackage extends PackageContract implements ThemeInterface
{
    use ThemePackageTrait;

    public function buildContainer(ContainerBuilder $containerBuilder): void
    {
        parent::buildContainer($containerBuilder);
        $containerBuilder->addCompilerPass(new ControllerPreparationCompilerPass());
        $containerBuilder->addCompilerPass(new TemplateTagCompilerPass());
        $containerBuilder->addCompilerPass(new TwigExtensionTagCompilerPass());
        $containerBuilder->addCompilerPass(new RemovePagesCompilerPass());
        $containerBuilder->addCompilerPass(new RegisterSuggestedTwigExtensionsCompilerPass());
    }

    public static function featureAll(ContainerBuilder $containerBuilder): void
    {
        self::featureTemplateCache($containerBuilder);
    }

    public static function featureDebug(ContainerBuilder $containerBuilder): void
    {
        self::featureTemplateDebug($containerBuilder);
    }

    public static function featureTemplateCache(ContainerBuilder $containerBuilder): void
    {
        $containerConfigurationPath = __DIR__ . '/Resources/config/feature/template';
        $xmlLoader = new XmlFileLoader($containerBuilder, new FileLocator($containerConfigurationPath));

        $xmlLoader->load($containerConfigurationPath . '/cache.xml');
    }

    public static function featureTemplateDebug(ContainerBuilder $containerBuilder): void
    {
        $containerConfigurationPath = __DIR__ . '/Resources/config/feature/template';
        $xmlLoader = new XmlFileLoader($containerBuilder, new FileLocator($containerConfigurationPath));

        $xmlLoader->load($containerConfigurationPath . '/debug.xml');
    }
}
