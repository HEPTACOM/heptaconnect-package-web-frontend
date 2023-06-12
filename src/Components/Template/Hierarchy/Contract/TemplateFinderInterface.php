<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\Contract;

interface TemplateFinderInterface
{
    /**
     * Finds the template file to render.
     *
     * @param string      $template Expects to start with "@ThemePackage"
     * @param string|null $source   When given, only template files after the source in regards of the inheritance order is next
     */
    public function find(string $template, bool $ignoreMissing = false, ?string $source = null): string;
}
