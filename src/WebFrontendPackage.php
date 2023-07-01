<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\BootstrapThemeFeature;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\CacheFeature;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\DebugFeature;
use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\ControllerPreparationCompilerPass;
use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\RegisterSuggestedTwigExtensionsCompilerPass;
use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\RemovePagesCompilerPass;
use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\TemplateTagCompilerPass;
use Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\TwigExtensionTagCompilerPass;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PackageContract;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class WebFrontendPackage extends PackageContract
{
    public function buildContainer(ContainerBuilder $containerBuilder): void
    {
        parent::buildContainer($containerBuilder);
        $containerBuilder->addCompilerPass(new ControllerPreparationCompilerPass());
        $containerBuilder->addCompilerPass(new TemplateTagCompilerPass());
        $containerBuilder->addCompilerPass(new TwigExtensionTagCompilerPass());
        $containerBuilder->addCompilerPass(new RemovePagesCompilerPass());
        $containerBuilder->addCompilerPass(new RegisterSuggestedTwigExtensionsCompilerPass());

        $containerBuilder->registerExtension(new CacheFeature());
        $containerBuilder->registerExtension(new DebugFeature());
        $containerBuilder->registerExtension(new BootstrapThemeFeature());
    }
}
