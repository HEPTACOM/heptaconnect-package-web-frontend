<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionManagerInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerUrlProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Ramsey\Uuid\Uuid;

final class LoginHandler extends HttpHandlerContract
{
    public function __construct(
        private UriFactoryInterface $uriFactory,
        private HttpHandlerUrlProviderInterface $urlProvider,
        private AccessProtectionServiceInterface $accessProtectionService,
        private SessionManagerInterface $sessionManager,
        private AuthorizationBackendInterface $authorizationBackend,
        private string $loginPath,
        private string $afterLoginPagePath
    ) {
    }

    protected function supports(): string
    {
        return $this->loginPath;
    }

    protected function get(
        ServerRequestInterface $request,
        ResponseInterface $response,
        HttpHandleContextInterface $context
    ): ResponseInterface {
        return $response->withStatus(302)
            ->withHeader('Location', (string) $this->urlProvider->resolve($this->afterLoginPagePath));
    }

    protected function post(
        ServerRequestInterface $request,
        ResponseInterface $response,
        HttpHandleContextInterface $context
    ): ResponseInterface {
        $username = $request->getParsedBody()['username'] ?? null;
        $password = $request->getParsedBody()['password'] ?? null;

        if (
            !\is_string($username) ||
            !\is_string($password) ||
            !$this->authorizationBackend->verify($username, $password)
        ) {
            return $this->getFailureResponse($request, $context);
        }

        $profile = [
            'id' => (string) Uuid::uuid5('f383a25af328482493ddab3d71ceef59', $username)->getHex(),
            'email' => $username . '@localhost',
            'username' => $username,
            'firstName' => '',
            'lastName' => '',
        ];

        $response = $this->getSuccessResponse($context);
        $sessionId = $response->getHeaderLine('X-Session-ID');
        $session = $this->sessionManager->getSessionFromId($sessionId);

        $session->set('profile', $profile);
        $this->sessionManager->saveSession($session);

        return $response;
    }

    private function getSuccessResponse(HttpHandleContextInterface $context): ResponseInterface
    {
        $loginUrl = $this->accessProtectionService->generateLoginUrl();
        $loginUri = $this->uriFactory->createUri($loginUrl)
            ->withScheme('')
            ->withHost('')
            ->withPort(null)
            ->withUserInfo('')
            ->withPath($this->afterLoginPagePath);

        $response = $context->forward($loginUri);

        if ($response->getStatusCode() === 307) {
            $response = $response->withStatus(302);
        }

        return $response;
    }

    private function getFailureResponse(
        ServerRequestInterface $request,
        HttpHandleContextInterface $context
    ): ResponseInterface {
        $uri = $this->uriFactory->createUri(LockscreenUiHandler::PATH)
            ->withScheme('')
            ->withHost('')
            ->withPort(null)
            ->withUserInfo('')
            ->withQuery(\http_build_query([
                'username' => (string) ($request->getParsedBody()['username'] ?? ''),
                'isError' => '1',
            ]));

        return $context->forward($uri);
    }
}
