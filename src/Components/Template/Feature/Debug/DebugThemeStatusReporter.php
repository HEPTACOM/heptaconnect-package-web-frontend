<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\Debug;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\ThemeCollection;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReporterContract;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReportingContextInterface;

final class DebugThemeStatusReporter extends StatusReporterContract
{
    public function __construct(
        private ThemeCollection $themes,
    ) {
    }

    public function supportsTopic(): string
    {
        return 'web-frontend:template:debug-theme';
    }

    protected function run(StatusReportingContextInterface $context): array
    {
        $themes = [];
        $themeErrors = [];

        foreach ($this->themes->getRenderOrder() as $theme) {
            $themeName = $theme->getThemeName();
            $themeData = [
                'templatesDir' => $theme->getThemeTemplatesPath(),
                'assetDir' => $theme->getThemeAssetPath(),
            ];

            if (isset($themes[$themeName])) {
                if (!isset($themeErrors[$themeName])) {
                    $themeErrors[$themeName] = [
                        $themes[$themeName],
                    ];
                }

                $themeErrors[$themeName][] = $themeData;
            }

            $themes[$themeName] = $themeData;
        }

        return [
            $this->supportsTopic() => $themeErrors === [],
            'themes' => $themes,
            'themeConflicts' => $themeErrors,
        ];
    }
}
