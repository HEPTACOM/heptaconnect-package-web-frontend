<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\View;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\DefaultPage;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class DefaultUiHandler extends UiHandlerContract
{
    public const PATH = 'ui';

    protected function supports(): string
    {
        return self::PATH;
    }

    protected function get(
        ServerRequestInterface $request,
        ResponseInterface $response,
        HttpHandleContextInterface $context
    ): ResponseInterface {
        return $this->render(new DefaultPage(), $request);
    }
}
