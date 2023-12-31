<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\UiHandlerContract;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionManagerInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\SessionManager;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalStorageInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpKernelInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerUrlProviderInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AccessProtectionMiddleware implements MiddlewareInterface
{
    private const STORAGE_PREFIX_AUTH_TOKEN = 'config.access-protection.auth_token.';

    private const QUERY_PARAMETER_AUTH_TOKEN = 'access_protection_token';

    private const SESSION_KEY_AUTHORIZED = 'authorized';

    /**
     * @var array<UiHandlerContract>
     */
    private array $uiHandlers;

    private bool $isAuthorized = false;

    /**
     * @param iterable<UiHandlerContract> $uiHandlers
     */
    public function __construct(
        iterable $uiHandlers,
        private string $loginPagePath,
        private PortalStorageInterface $portalStorage,
        private ResponseFactoryInterface $responseFactory,
        private HttpHandlerUrlProviderInterface $urlProvider,
        private HttpKernelInterface $httpKernel,
        private ServerRequestFactoryInterface $serverRequestFactory,
        private SessionManagerInterface $sessionManager
    ) {
        $this->uiHandlers = \iterable_to_array($uiHandlers);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isAuthorized || $this->isAllowed($request)) {
            return $handler->handle($request);
        }

        if ($this->hasAccessToken($request)) {
            $redirectUri = $this->urlProvider
                ->resolve($request->getUri()->getPath())
                ->withQuery($request->getUri()->getQuery());

            $redirectUri = $this->withoutAccessToken($redirectUri);

            $response = $this->responseFactory->createResponse(307)
                ->withHeader('Location', (string) $redirectUri);

            if ($this->hasAuthorizedAccessToken($request)) {
                return $this->withAuthorizedSession($request, $response);
            }

            return $response;
        }

        if (!$this->hasAuthorizedSession($request)) {
            return $this->createUnauthorizedResponse();
        }

        $session = $this->getAuthorizedSession($request);
        $request = $request->withAttribute(SessionManager::REQUEST_ATTRIBUTE_SESSION, $session);

        return $this->handle($handler, $request);
    }

    private function isAllowed(ServerRequestInterface $request): bool
    {
        $requestPath = $request->getUri()->getPath();

        foreach ($this->uiHandlers as $ui) {
            if ($requestPath !== $ui->getPath()) {
                continue;
            }

            return !$ui->isProtected($request);
        }

        return true;
    }

    private function handle(RequestHandlerInterface $handler, ServerRequestInterface $request): ResponseInterface
    {
        $wasAuthorized = $this->isAuthorized;
        $this->isAuthorized = true;

        try {
            $response = $handler->handle($request);
        } finally {
            $this->isAuthorized = $wasAuthorized;
        }

        return $response;
    }

    private function hasAccessToken(ServerRequestInterface $request): bool
    {
        $accessProtectionToken = $request->getQueryParams()[self::QUERY_PARAMETER_AUTH_TOKEN] ?? null;

        if (!\is_string($accessProtectionToken)) {
            return false;
        }

        return true;
    }

    private function hasAuthorizedAccessToken(ServerRequestInterface $request): bool
    {
        $accessProtectionToken = $request->getQueryParams()[self::QUERY_PARAMETER_AUTH_TOKEN] ?? null;

        if (!\is_string($accessProtectionToken)) {
            return false;
        }

        $credentials = \base64_decode($accessProtectionToken, true);

        if (!\is_string($credentials)) {
            return false;
        }

        [$username, $password] = \explode(':', $credentials, 2);
        $hashedPassword = $this->portalStorage->get(self::STORAGE_PREFIX_AUTH_TOKEN . $username);

        if (!\is_string($hashedPassword)) {
            return false;
        }

        if (!\password_verify($password, $hashedPassword)) {
            return false;
        }

        $this->portalStorage->delete(self::STORAGE_PREFIX_AUTH_TOKEN . $username);

        return true;
    }

    private function withoutAccessToken(UriInterface $uri): UriInterface
    {
        \parse_str($uri->getQuery(), $queryParams);
        unset($queryParams[self::QUERY_PARAMETER_AUTH_TOKEN]);

        return $uri->withQuery(\http_build_query($queryParams));
    }

    private function hasAuthorizedSession(ServerRequestInterface $request): bool
    {
        $session = $this->sessionManager->getSessionFromRequest($request);

        return $session?->has(self::SESSION_KEY_AUTHORIZED) ?? false;
    }

    private function withAuthorizedSession(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        return $this->sessionManager->alterResponse($this->getAuthorizedSession($request), $response);
    }

    private function createUnauthorizedResponse(): ResponseInterface
    {
        return $this->httpKernel->handle(
            $this->serverRequestFactory->createServerRequest('GET', $this->loginPagePath)
        )->withStatus(401);
    }

    private function getAuthorizedSession(ServerRequestInterface $request): SessionInterface
    {
        $session = $this->sessionManager->getSessionFromRequest($request) ?? $this->sessionManager->createSession($request);

        $session->set(self::SESSION_KEY_AUTHORIZED, true);

        if (!$session->has('profile')) {
            $session->set('profile', [
                'id' => \md5(uniqid()),
                'email' => 'root@localhost',
                'username' => 'root',
                'firstName' => '',
                'lastName' => '',
            ]);
        }

        return $session;
    }
}
