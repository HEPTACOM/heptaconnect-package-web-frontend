<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\Contract\AccessProtectionServiceInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\Contract\AuthorizationBackendInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionManagerInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerUrlProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;

final class LoginHandler extends HttpHandlerContract
{
    public function __construct(
        private UriFactoryInterface $uriFactory,
        private HttpHandlerUrlProviderInterface $urlProvider,
        private AccessProtectionServiceInterface $accessProtectionService,
        private SessionManagerInterface $sessionManager,
        private AuthorizationBackendInterface $authorizationBackend,
        private string $loginPath,
        private string $loginPagePath,
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
            !\is_string($username)
            || !\is_string($password)
            || !$this->authorizationBackend->verify($username, $password)
        ) {
            return $this->getFailureResponse($request, $context);
        }

        $profile = [
            'id' => \md5($username),
            'email' => $username . '@localhost',
            'username' => $username,
            'firstName' => '',
            'lastName' => '',
        ];

        $session = $this->sessionManager->getSessionFromRequest($request) ?? $this->sessionManager->createSession($request);

        if ($session === null) {
            throw new \UnexpectedValueException('Session could not be fetched to add profile data', 1688914000);
        }

        $session->set('authorized', true);
        $session->set('profile', $profile);
        $this->sessionManager->saveSession($session);

        return $response->withStatus(302)
            ->withHeader('Location', (string) $this->urlProvider->resolve($this->afterLoginPagePath));
    }

    private function getFailureResponse(
        ServerRequestInterface $request,
        HttpHandleContextInterface $context
    ): ResponseInterface {
        $uri = $this->uriFactory->createUri($this->loginPagePath)
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
