<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\ThemeCollection;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\TwigEnvironmentFactoryInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\ExtendsTokenParser;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\IncludeTokenParser;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\TemplateFinder;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerUrlProviderInterface;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Extra\String\StringExtension;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

final class TwigEnvironmentFactory implements TwigEnvironmentFactoryInterface
{
    private ThemeCollection $themes;

    /**
     * @var array<ExtensionInterface>
     */
    private array $extensions;

    public function __construct(
        ThemeCollection $themes,
        iterable $extensions,
        private HttpHandlerUrlProviderInterface $urlProvider,
        private AssetMiddleware $assetMiddleware,
    ) {
        $this->themes = $themes->getRenderOrder();
        $this->extensions = \iterable_to_array($extensions);
    }

    public function createTwigEnvironment(): Environment
    {
        $chainLoader = new ChainLoader();

        foreach ($this->themes as $theme) {
            $loader = new FilesystemLoader();
            $loader->setPaths($theme->getThemeTemplatesPath(), $theme->getThemeName());
            $chainLoader->addLoader($loader);
        }

        $environment = new Environment($chainLoader);

        $environment->addFunction(new TwigFunction(
            'asset',
            function (string $assetPath): string {
                return $this->createAssetUrl($assetPath);
            }
        ));

        $environment->addFunction(new TwigFunction(
            'path',
            function (string $path, array $parameters = []): string {
                return $this->createPathUrl($path, $parameters);
            }
        ));

        $environment->addExtension(new StringExtension());

        foreach ($this->extensions as $extension) {
            $environment->addExtension($extension);
        }

        $templateFinder = new TemplateFinder($environment->getLoader(), $this->themes);
        $environment->addTokenParser(new ExtendsTokenParser($templateFinder));
        $environment->addTokenParser(new IncludeTokenParser($templateFinder));

        return $environment;
    }

    private function createAssetUrl(string $assetPath): string
    {
        $assetPath = \ltrim($assetPath, '/');
        $fileHash = $this->assetMiddleware->getFileHash($assetPath);
        $queryParameters = [];

        if (\is_string($fileHash)) {
            $queryParameters['v'] = $fileHash;
        }

        $query = \http_build_query($queryParameters);

        return (string) $this->urlProvider
            ->resolve($this->assetMiddleware->getAssetUrlPath() . $assetPath)
            ->withQuery($query);
    }

    private function createPathUrl(string $path, array $parameters = []): string
    {
        $pathUri = $this->urlProvider->resolve($path);

        if ($parameters !== []) {
            $pathUri = $pathUri->withQuery(\http_build_query($parameters));
        }

        return (string) $pathUri;
    }
}
