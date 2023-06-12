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

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @throws \ReflectionException
     */
    public function isInstanceOf(mixed $var, string $class): bool
    {
        if ($var === null) {
            return false;
        }

        if (!\is_object($var)) {
            return false;
        }

        return (new \ReflectionClass($class))->isInstance($var);
    }
}
