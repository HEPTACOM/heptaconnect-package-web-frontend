<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy;

use Heptacom\HeptaConnect\Dataset\Base\ScalarCollection\StringCollection;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\ThemeCollection;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\Contract\TemplateFinderInterface;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;

final class TemplateFinder implements TemplateFinderInterface
{
    private StringCollection $namespaceHierarchy;

    public function __construct(
        private LoaderInterface $loader,
        private ThemeCollection $themes,
    ) {
        $this->namespaceHierarchy = $this->themes->getRenderOrder()->getNames();
    }

    public function find(string $template, bool $ignoreMissing = false, ?string $source = null): string
    {
        $templatePath = $this->getTemplateName($template);
        $sourcePath = null;
        $sourceBundleName = null;
        $originalTemplate = $template;

        if ($source !== null) {
            $sourcePath = $this->getTemplateName($source);
            $sourceBundleName = $this->getSourceBundleName($source);
            $originalTemplate = null;
        }

        $queue = $this->namespaceHierarchy->asArray();
        $modifiedQueue = $queue;

        // If we are trying to load the same file as the template, we do are not allowed to search the hierarchy
        // up to the source file as that has already been searched and that would lead to an endless template inheritance.

        if ($sourceBundleName !== null && $sourcePath === $templatePath) {
            $index = \array_search($sourceBundleName, $modifiedQueue, true);

            if (\is_int($index)) {
                $modifiedQueue = \array_merge(\array_slice($modifiedQueue, $index + 1), \array_slice($queue, 0, $index));
            }
        }

        // iterate over all bundles but exclude the originally requested bundle
        // example: if @Storefront/storefront/index.html.twig is requested, all bundles except Storefront will be checked first
        foreach ($modifiedQueue as $prefix) {
            $name = '@' . $prefix . '/' . $templatePath;

            // original template is loaded last
            if ($name === $originalTemplate) {
                continue;
            }

            if (!$this->loader->exists($name)) {
                continue;
            }

            return $name;
        }

        // Throw a useful error when the template cannot be found
        if ($originalTemplate === null) {
            if ($ignoreMissing) {
                return $templatePath;
            }

            throw new LoaderError(\sprintf('Unable to load template "%s". (Looked into: %s)', $templatePath, implode(', ', array_values($modifiedQueue))));
        }

        // if no other bundle extends the requested template, load the original template
        if ($this->loader->exists($originalTemplate)) {
            return $originalTemplate;
        }

        if ($ignoreMissing) {
            return $templatePath;
        }

        throw new LoaderError(\sprintf('Unable to load template "%s". (Looked into: %s)', $templatePath, implode(', ', array_values($modifiedQueue))));
    }

    private function getTemplateName(string $template): string
    {
        // remove static template inheritance prefix
        if (mb_strpos($template, '@') !== 0) {
            return $template;
        }

        $template = explode('/', $template, 2);

        return $template[1] ?? $template[0];
    }

    private function getSourceBundleName(string $source): ?string
    {
        if (\mb_strpos($source, '@') !== 0) {
            return null;
        }

        $source = explode('/', $source, 2);

        return \ltrim($source[0], '@');
    }
}
