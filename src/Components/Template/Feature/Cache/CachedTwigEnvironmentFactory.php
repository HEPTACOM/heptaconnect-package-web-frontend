<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\Cache;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\TwigEnvironmentFactoryInterface;
use Heptacom\HeptaConnect\Portal\Base\File\Filesystem\Contract\FilesystemInterface;
use Twig\Environment;

final class CachedTwigEnvironmentFactory implements TwigEnvironmentFactoryInterface
{
    public function __construct(
        private TwigEnvironmentFactoryInterface $decorated,
        private FilesystemInterface $filesystem,
        private CachePath $cachePath,
    ) {
    }

    public function createTwigEnvironment(): Environment
    {
        $result = $this->decorated->createTwigEnvironment();

        $result->setCache(new TwigCache($this->filesystem->toStoragePath($this->cachePath->getCurrentCachePath())));

        return $result;
    }
}
