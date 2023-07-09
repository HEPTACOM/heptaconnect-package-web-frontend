<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionManagerInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerStackInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerUrlProviderInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class LogoutHandler extends HttpHandlerContract
{
    public function __construct(
        private string $afterLogoutPagePath,
        private string $logoutPath,
        private ResponseFactoryInterface $responseFactory,
        private HttpHandlerUrlProviderInterface $urlProvider,
        private SessionManagerInterface $sessionManager
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        HttpHandleContextInterface $context,
        HttpHandlerStackInterface $stack
    ): ResponseInterface {
        $this->sessionManager->destroySession($request);

        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', (string) $this->urlProvider->resolve($this->afterLogoutPagePath));
    }

    protected function supports(): string
    {
        return $this->logoutPath;
    }
}
