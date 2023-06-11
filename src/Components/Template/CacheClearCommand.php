<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template;

use Heptacom\HeptaConnect\Portal\Base\File\Filesystem\Contract\FilesystemInterface;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReporterContract;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReportingContextInterface;

final class CacheClearCommand extends StatusReporterContract
{
    public function __construct(
        private FilesystemInterface $filesystem,
    ) {
    }

    public function supportsTopic(): string
    {
        return 'cache:clear';
    }

    protected function run(StatusReportingContextInterface $context): array
    {
        $this->delete($this->filesystem->toStoragePath('var/cache/templates'));

        return [$this->supportsTopic() => true];
    }

    private function delete(string $path): void
    {
        $path = \rtrim($path, \DIRECTORY_SEPARATOR);

        foreach (scandir($path) as $node) {
            if (\in_array($node, ['.', '..'], true)) {
                continue;
            }

            $nodePath = $path . \DIRECTORY_SEPARATOR . $node;

            if (\is_dir($nodePath)) {
                $this->delete($nodePath);

                \rmdir($nodePath);
            } elseif (\is_file($nodePath)) {
                \unlink($nodePath);
            }
        }
    }
}