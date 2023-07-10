<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\Contract\TemplateFinderInterface;
use Twig\Extension\AbstractExtension;

final class NodeExtension extends AbstractExtension
{
    public function __construct(
        private TemplateFinderInterface $finder,
    ) {
    }

    public function getTokenParsers(): array
    {
        return [
            new ExtendsTokenParser($this->finder),
            new IncludeTokenParser($this->finder),
        ];
    }

    /**
     * @see InheritedInclude
     */
    public function getFinder(): TemplateFinderInterface
    {
        return $this->finder;
    }
}
