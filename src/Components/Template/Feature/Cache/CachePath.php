<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\Cache;

use Psr\SimpleCache\CacheInterface;

class CachePath
{
    private const TWIG_CACHE_TAG_KEY = 'web_frontend_template_twig_cache_tag';

    private string $cacheDir;

    public function __construct(
        string $cacheDir,
        private CacheInterface $cache,
    ) {
        $this->cacheDir = \rtrim($cacheDir, '/\\') . \DIRECTORY_SEPARATOR;
    }

    public function getOriginalPath(): string
    {
        return $this->cacheDir;
    }

    public function getCurrentCachePath(): string
    {
        $tag = $this->cache->get(self::TWIG_CACHE_TAG_KEY);

        if ($tag === null) {
            $tag = $this->createKey();
            $this->cache->set(self::TWIG_CACHE_TAG_KEY, $tag);
        }

        return $this->cacheDir . $tag . \DIRECTORY_SEPARATOR;
    }

    public function resetTag(): void
    {
        $this->cache->set(self::TWIG_CACHE_TAG_KEY, $this->createKey());
    }

    private function createKey(): string
    {
        return \bin2hex(\random_bytes(8));
    }
}
