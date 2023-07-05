<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract;

use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

interface WebPageTwigEnvironmentFactoryInterface
{
    /**
     * Create a twig environment, that is meant to render a web page for the given request.
     */
    public function createTwigEnvironment(
        ServerRequestInterface $request,
        HttpHandleContextInterface $context
    ): Environment;
}
