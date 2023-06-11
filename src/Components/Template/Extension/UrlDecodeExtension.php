<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class UrlDecodeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('url_decode', 'urldecode'),
        ];
    }
}
