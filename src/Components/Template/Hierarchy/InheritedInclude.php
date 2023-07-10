<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy;

use Twig\Compiler;
use Twig\Node\IncludeNode;

final class InheritedInclude extends IncludeNode
{
    protected function addGetTemplate(Compiler $compiler): void
    {
        $compiler
            ->write("((function () use (\$context, \$blocks) {\n")
            ->indent()
            ->write('$finder = $this->env->getExtension(\'' . NodeExtension::class . '\')->getFinder();')->raw("\n\n")
            ->write('$includeTemplate = $finder->find(')
            ->subcompile($this->getNode('expr'))
            ->raw(");\n\n")
            ->write('return $this->loadTemplate(')
            ->raw('$includeTemplate ?? null, ')
            ->repr($this->getTemplateName())->raw(', ')
            ->repr($this->getTemplateLine())
            ->raw(");\n")
            ->outdent()
            ->write('})())');
    }
}
