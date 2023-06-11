<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy;

use Twig\Compiler;
use Twig\Node\Node;
use Twig\Node\NodeOutputInterface;

final class ReturnNode extends Node implements NodeOutputInterface
{
    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write('return ');

        if ($this->hasNode('expr')) {
            $compiler->subcompile($this->getNode('expr'));
            $compiler->raw(";\n");

            return;
        }

        $compiler->raw(";\n");
    }
}
