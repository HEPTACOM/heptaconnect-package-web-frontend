<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\DefaultPage;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\UiHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class DefaultUiHandler extends UiHandlerContract
{
    public function __construct(
        private string $defaultPagePath
    ) {
    }

    protected function supports(): string
    {
        return $this->defaultPagePath;
    }

    protected function get(
        ServerRequestInterface $request,
        ResponseInterface $response,
        HttpHandleContextInterface $context
    ): ResponseInterface {
        return $this->render(new DefaultPage());
    }
}
