<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy;

use Twig\Node\Node;
use Twig\Parser;
use Twig\Token;
use Twig\TokenParser\TokenParserInterface;

final class TokenParserDecorator implements TokenParserInterface
{
    public function __construct(
        private TokenParserInterface $parser,
        private string $tag
    ) {
    }

    public function setParser(Parser $parser): void
    {
        $this->parser->setParser($parser);
    }

    public function parse(Token $token): Node
    {
        return $this->parser->parse($token);
    }

    public function getTag(): string
    {
        return $this->tag;
    }
}
