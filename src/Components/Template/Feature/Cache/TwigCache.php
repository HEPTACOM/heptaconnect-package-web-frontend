<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\Cache;

use Twig\Cache\FilesystemCache;

final class TwigCache extends FilesystemCache
{
    public function __construct(
        string $directory,
        private int $options = 0,
    ) {
        parent::__construct($directory, $this->options);
    }

    public function write(string $key, string $content): void
    {
        $dir = \dirname($key);
        if (!\is_dir($dir)) {
            if (@\mkdir($dir, 0777, true) === false) {
                \clearstatcache(true, $dir);
                if (!\is_dir($dir)) {
                    throw new \RuntimeException(\sprintf('Unable to create the cache directory (%s).', $dir));
                }
            }
        } elseif (!\is_writable($dir)) {
            throw new \RuntimeException(\sprintf('Unable to write in the cache directory (%s).', $dir));
        }

        $tmpFile = $this->tempnam($dir, \basename($key));
        if ($tmpFile !== null && @\file_put_contents($tmpFile, $content) !== false && @\rename($tmpFile, $key)) {
            @\chmod($key, 0666 & ~\umask());

            if (self::FORCE_BYTECODE_INVALIDATION === ($this->options & self::FORCE_BYTECODE_INVALIDATION)) {
                // Compile cached file into bytecode cache
                if (\function_exists('opcache_invalidate') && \filter_var(\ini_get('opcache.enable'), \FILTER_VALIDATE_BOOLEAN)) {
                    @\opcache_invalidate($key, true);
                } elseif (\function_exists('apc_compile_file')) {
                    \apc_compile_file($key);
                }
            }

            return;
        }

        throw new \RuntimeException(\sprintf('Failed to write cache file "%s".', $key));
    }

    private function tempnam(string $directory, string $prefix): ?string
    {
        if ($directory === '') {
            $directory = \sys_get_temp_dir();

            \trigger_error(
                __METHOD__ . '(): file created in the system\'s temporary directory',
                \E_NOTICE
            );
        }

        $directory = \rtrim($directory, '/') . '/';
        $prefix = \ltrim($prefix, '/');

        $i = 0;

        do {
            $filename = $directory . $prefix . \bin2hex(\random_bytes(16));

            if (!\file_exists($filename)) {
                \touch($filename);
                \chmod($filename, 0600);

                return $filename;
            }
        } while (++$i < 10);

        return null;
    }
}
