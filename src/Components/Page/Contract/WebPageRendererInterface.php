<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\AbstractPage;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface WebPageRendererInterface
{
    /**
     * Renders a page in a web context.
     */
    public function render(
        AbstractPage $page,
        ServerRequestInterface $request,
        HttpHandleContextInterface $context
    ): ResponseInterface;
}
