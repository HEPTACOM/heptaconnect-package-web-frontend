<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

final class IsNumericExtension extends AbstractExtension
{
    public function getTests(): array
    {
        return [
            new TwigTest('numeric', 'is_numeric'),
        ];
    }
}
