<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\View;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\ThemeInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class AssetHandler extends HttpHandlerContract
{
    public const PATH = '_asset';

    /**
     * @var array<string>
     */
    private array $staticFilesPaths = [];

    /**
     * @param iterable<ThemeInterface> $themes
     */
    public function __construct(
        iterable $themes,
        private StreamFactoryInterface $streamFactory
    ) {
        foreach ($themes as $theme) {
            $this->staticFilesPaths[] = \rtrim($theme->getThemeAssetPath(), '/') . '/';
        }
    }

    public function getFileHash(string $filePath): string|false
    {
        return \md5_file($this->getRealFilePath($filePath));
    }

    protected function supports(): string
    {
        return self::PATH;
    }

    protected function get(
        ServerRequestInterface $request,
        ResponseInterface $response,
        HttpHandleContextInterface $context
    ): ResponseInterface {
        $relativeFilePath = $request->getQueryParams()['file'] ?? null;
        $notFoundResponse = $response->withStatus(404);

        if (!\is_string($relativeFilePath)) {
            return $notFoundResponse;
        }

        $realFilePath = $this->getRealFilePath($relativeFilePath);

        if (!\is_string($realFilePath)) {
            return $notFoundResponse;
        }

        $response = $response->withHeader('Cache-Control', 'public, max-age=15552000');

        $etag = $this->getEtag($relativeFilePath);

        if ($request->getHeaderLine('If-None-Match') === $etag) {
            return $response->withStatus(304);
        }

        $response = $response->withHeader('ETag', $etag);

        $stream = $this->streamFactory->createStreamFromFile($realFilePath);

        $contentType = \finfo_buffer(\finfo_open(\FILEINFO_MIME_TYPE), (string) $stream);
        $response = $response->withHeader('Content-Type', $contentType);

        return $response->withStatus(200)->withBody($stream);
    }

    private function getEtag(string $relativeFilePath): string
    {
        return \sprintf('W/"%s"', $this->getFileHash($relativeFilePath));
    }

    private function getRealFilePath(string $relativeFilePath): ?string
    {
        foreach ($this->staticFilesPaths as $staticFilesPath) {
            $absoluteFilePath = $staticFilesPath . \ltrim($relativeFilePath, '/');
            $realFilePath = \realpath($absoluteFilePath);

            if (!\is_string($realFilePath)) {
                continue;
            }

            if (!\str_starts_with($realFilePath, $staticFilesPath)) {
                continue;
            }

            return $realFilePath;
        }

        return null;
    }
}
