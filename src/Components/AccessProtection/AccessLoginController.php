<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\SessionManagerInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\View\DefaultUiHandler;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\View\LockscreenUiHandler;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerUrlProviderInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

final class AccessLoginController extends HttpHandlerContract
{
    public const PATH = '_access/login';

    private const JSON_FLAGS = \JSON_THROW_ON_ERROR | \JSON_PRESERVE_ZERO_FRACTION | \JSON_UNESCAPED_SLASHES;

    public function __construct(
        private UriFactoryInterface $uriFactory,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private HttpHandlerUrlProviderInterface $urlProvider,
        private ClientInterface $httpClient,
        private AccessProtectionServiceInterface $accessProtectionService,
        private SessionManagerInterface $sessionManager
    ) {
    }

    protected function supports(): string
    {
        return self::PATH;
    }

    protected function get(
        ServerRequestInterface $request,
        ResponseInterface $response,
        HttpHandleContextInterface $context
    ): ResponseInterface {
        return $response->withStatus(302)
            ->withHeader('Location', (string) $this->urlProvider->resolve(DefaultUiHandler::PATH));
    }

    protected function post(
        ServerRequestInterface $request,
        ResponseInterface $response,
        HttpHandleContextInterface $context
    ): ResponseInterface {
        $authorizationHeader = $this->getAuthorizationHeader(
            $request->getParsedBody()['username'] ?? null,
            $request->getParsedBody()['password'] ?? null
        );

        if ($authorizationHeader === null) {
            return $this->getFailureResponse($request, $context);
        }

        $profile = $this->getProfile($authorizationHeader);

        $response = $this->getSuccessResponse($context);

        $sessionId = $response->getHeaderLine('X-Session-ID');
        $session = $this->sessionManager->restoreSession($sessionId);
        $session->getStorage()->set('profile', $profile);

        return $response;
    }

    private function getAuthorizationHeader(?string $username, ?string $password): ?string
    {
        $tokenUri = $this->urlProvider->resolve('')->withPath('/api/oauth/token');

        $authRequest = $this->requestFactory->createRequest('POST', $tokenUri)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream(\json_encode([
                'client_id' => 'administration',
                'grant_type' => 'password',
                'scopes' => 'write',
                'username' => $username,
                'password' => $password,
            ])));

        $authResponse = $this->httpClient->sendRequest($authRequest);

        if ($authResponse->getStatusCode() !== 200) {
            return null;
        }

        $authResponseData = \json_decode(
            (string) $authResponse->getBody(),
            true,
            512,
            self::JSON_FLAGS
        );

        $tokenType = $authResponseData['token_type'];
        $accessToken = $authResponseData['access_token'];

        return $tokenType . ' ' . $accessToken;
    }

    private function getProfile(string $authorizationHeader): array
    {
        $profileUri = $this->urlProvider->resolve('')->withPath('/api/_info/me');

        $profileRequest = $this->requestFactory->createRequest('GET', $profileUri)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', $authorizationHeader);

        $profileResponse = $this->httpClient->sendRequest($profileRequest);
        $profileData = \json_decode(
            (string) $profileResponse->getBody(),
            true,
            512,
            self::JSON_FLAGS
        );

        $profile = $profileData['data'];

        $profile = \array_intersect_key($profile, \array_flip([
            'id',
            'email',
            'username',
            'firstName',
            'lastName',
        ]));

        return $profile;
    }

    private function getSuccessResponse(HttpHandleContextInterface $context): ResponseInterface
    {
        $loginUrl = $this->accessProtectionService->generateLoginUrl();
        $loginUri = $this->uriFactory->createUri($loginUrl)
            ->withScheme('')
            ->withHost('')
            ->withPort(null)
            ->withUserInfo('')
            ->withPath(DefaultUiHandler::PATH);

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
