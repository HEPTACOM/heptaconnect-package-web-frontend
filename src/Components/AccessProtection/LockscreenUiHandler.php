<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\UiHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class LockscreenUiHandler extends UiHandlerContract
{
    public function __construct(
        private string $loginPagePath
    ) {
    }

    public function isProtected(ServerRequestInterface $request): bool
    {
        return false;
    }

    protected function supports(): string
    {
        return $this->loginPagePath;
    }

    protected function get(
        ServerRequestInterface $request,
        ResponseInterface $response,
        HttpHandleContextInterface $context
    ): ResponseInterface {
        $page = new LockscreenPage(
            (string) ($request->getQueryParams()['username'] ?? ''),
            (bool) ($request->getQueryParams()['isError'] ?? false)
        );

        return $this->render($page)->withStatus($page->isError() ? 401 : 200);
    }
}
