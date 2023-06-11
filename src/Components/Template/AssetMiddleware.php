<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\ThemeCollection;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AssetMiddleware implements MiddlewareInterface
{
    /**
     * @var string[]
     */
    private array $staticFilesPaths = [];

    private string $assetUrlPath;

    public function __construct(
        string $assetUrlPath,
        ThemeCollection $themes,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
    ) {
        $this->assetUrlPath = \rtrim($assetUrlPath, '/') . '/';

        foreach ($themes->getRenderOrder() as $theme) {
            $this->staticFilesPaths[] = \rtrim($theme->getThemeAssetPath(), '/') . '/';
        }
    }

    public function getAssetUrlPath(): string
    {
        return $this->assetUrlPath;
    }

    public function getFileHash(string $filePath): ?string
    {
        $result = \md5_file($this->getRealFilePath(\ltrim($filePath, '/')));

        return $result === false ? null : $result;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        if (!\str_starts_with($path, $this->assetUrlPath)) {
            return $handler->handle($request);
        }

        return $this->processFile($request);
    }

    private function processFile(ServerRequestInterface $request): ResponseInterface
    {
        $relativeFilePath = \ltrim(\mb_substr($request->getUri()->getPath(), \mb_strlen($this->assetUrlPath)), '/');
        $realFilePath = $this->getRealFilePath($relativeFilePath);

        if (!\is_string($realFilePath)) {
            return $this->responseFactory->createResponse(404);
        }

        $response = $this->responseFactory->createResponse();
        $response = $response->withHeader('Cache-Control', 'public, max-age=15552000');
        $etag = $this->getEtag($relativeFilePath);

        if ($etag !== null) {
            if ($request->getHeaderLine('If-None-Match') === $etag) {
                return $response->withStatus(304);
            }

            $response = $response->withHeader('ETag', $etag);
        }

        $stream = $this->streamFactory->createStreamFromFile($realFilePath);

        if (\extension_loaded('fileinfo')) {
            $contentType = \finfo_buffer(\finfo_open(\FILEINFO_MIME_TYPE), (string) $stream);
            $response = $response->withHeader('Content-Type', $contentType);
        }

        return $response->withStatus(200)->withBody($stream);
    }

    private function getEtag(string $relativeFilePath): ?string
    {
        $hash = $this->getFileHash($relativeFilePath);

        return $hash === null ? null : \sprintf('W/"%s"', $hash);
    }

    private function getRealFilePath(string $relativeFilePath): ?string
    {
        foreach ($this->staticFilesPaths as $staticFilesPath) {
            $absoluteFilePath = $staticFilesPath . $relativeFilePath;
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
