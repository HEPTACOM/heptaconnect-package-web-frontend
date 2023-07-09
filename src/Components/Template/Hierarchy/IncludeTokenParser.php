<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\Contract\TemplateFinderInterface;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\IncludeNode;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

final class IncludeTokenParser extends AbstractTokenParser
{
    public function __construct(
        private TemplateFinderInterface $finder,
    ) {
    }

    public function parse(Token $token): Node
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        if (!$expr instanceof AbstractExpression) {
            throw new \UnexpectedValueException(\sprintf('parsedExpression needs to be an "%s"', AbstractExpression::class));
        }

        [$variables, $only, $ignoreMissing] = $this->parseArguments();

        // resolves parent template
        if ($expr->hasAttribute('value')) {
            // set pointer to next value (contains the template file name)
            $parent = $this->finder->find((string) $expr->getAttribute('value'), $ignoreMissing);

            $expr->setAttribute('value', $parent);

            return new IncludeNode($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
        }

        return new SwInclude($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'include';
    }

    /**
     * @return array{
     *     AbstractExpression|null,
     *     bool,
     *     bool
     * }
     */
    private function parseArguments(): array
    {
        $stream = $this->parser->getStream();
        $ignoreMissing = false;
        $variables = null;
        $only = false;

        if ($stream->nextIf(Token::NAME_TYPE, 'ignore') !== null) {
            $stream->expect(Token::NAME_TYPE, 'missing');

            $ignoreMissing = true;
        }

        if ($stream->nextIf(Token::NAME_TYPE, 'with') !== null) {
            $variables = $this->parser->getExpressionParser()->parseExpression();

            if (!$variables instanceof AbstractExpression) {
                $variables = null;
            }
        }

        if ($stream->nextIf(Token::NAME_TYPE, 'only') !== null) {
            $only = true;
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return [$variables, $only, $ignoreMissing];
    }
}
