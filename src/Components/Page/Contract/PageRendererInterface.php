<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface PageRendererInterface
{
    public function render(AbstractPage $page, ?ServerRequestInterface $request = null): ResponseInterface;
}
