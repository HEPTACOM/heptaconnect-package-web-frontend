<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

final class InstanceOfExtension extends AbstractExtension
{
    public function getTests(): array
    {
        return [
            'instanceof' => new TwigTest('instanceof', [
                $this, 'isInstanceOf',
            ]),
        ];
    }

    public function isInstanceOf($var, $class): bool
    {
        if ($var === null) {
            return false;
        }

        return (new \ReflectionClass($class))->isInstance($var);
    }
}
