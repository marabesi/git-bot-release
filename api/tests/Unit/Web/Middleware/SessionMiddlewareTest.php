<?php
declare(strict_types=1);

namespace Tests\Unit\Web\Middleware;

use App\Domain\Gitlab\Authentication\TokenNotFound;
use App\Domain\Gitlab\Authentication\TokenRepository;
use App\Domain\Gitlab\Version\VersionRepository;
use App\Infrastructure\Gateway\Gitlab\Exception\FailedToFetchVersion;
use App\Web\Middleware\SessionMiddleware;
use App\Web\Routes;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ServerRequestFactory;

class SessionMiddlewareTest extends TestCase
{

    private TokenRepository $tokenRepository;
    private VersionRepository $versionRepository;
    private RequestHandlerInterface $requestHandler;

    public function setUp(): void
    {
        $this->tokenRepository = $this->createStub(TokenRepository::class);
        $this->versionRepository = $this->createStub(VersionRepository::class);
        $this->requestHandler = $this->createMock(RequestHandlerInterface::class);
        parent::setUp();
    }

    public function allowedRoutes(): array
    {
        return array_map(fn($route) => [$route[0]], Routes::getAllowedRoutes());
    }

    public function test_should_redirect_if_not_authenticated()
    {
        $this->tokenRepository->method('getToken')
            ->willThrowException(new TokenNotFound());

        $this->versionRepository->method('fetchCurrent')
            ->will($this->returnValue([]));

        $this->requestHandler->expects($this->never())
            ->method('handle');

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/projects', []);
        $sessionManager = new SessionMiddleware($this->tokenRepository, $this->versionRepository);
        $response = $sessionManager->process($request, $this->requestHandler);

        $this->assertEquals('/', $response->getHeaderLine('Location'));
    }

    /**
     * @dataProvider allowedRoutes
     */
    public function test_process_request_when_requesting_allowed_routes($route)
    {
        $this->tokenRepository->method('getToken')
            ->willReturn('');

        $this->versionRepository->method('fetchCurrent')
            ->will($this->returnValue([]));

        $this->requestHandler->expects($this->once())
            ->method('handle');

        $request = (new ServerRequestFactory())->createServerRequest('GET', $route, []);
        $sessionManager = new SessionMiddleware($this->tokenRepository, $this->versionRepository);
        $response = $sessionManager->process($request, $this->requestHandler);

        $this->assertNull($response->getHeaderLine('Location'));
    }

    public function test_should_process_request_if_authenticated()
    {
        $this->tokenRepository->method('getToken')
            ->willReturn('my_token');

        $this->versionRepository->method('fetchCurrent')
            ->will($this->returnValue([]));

        $this->requestHandler->expects($this->once())
            ->method('handle');

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/projects', []);
        $sessionManager = new SessionMiddleware($this->tokenRepository, $this->versionRepository);
        $response = $sessionManager->process($request, $this->requestHandler);

        $this->assertNull($response->getHeaderLine('Location'));
    }

    public function test_redirect_if_error_fetching_current_gitlab_version_and_requested_path_is_not_allowed()
    {
        $this->tokenRepository->method('getToken')
            ->willReturn('my_token');

        $this->versionRepository->method('fetchCurrent')
            ->willThrowException(new FailedToFetchVersion());

        $this->requestHandler->expects($this->never())
            ->method('handle');

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/projects', []);
        $sessionManager = new SessionMiddleware($this->tokenRepository, $this->versionRepository);
        $response = $sessionManager->process($request, $this->requestHandler);

        $this->assertEquals('/', $response->getHeaderLine('Location'));
    }

    public function test_process_request_if_error_fetching_current_gitlab_version_and_requested_path_is_allowed()
    {
        $this->tokenRepository->method('getToken')
            ->willReturn('my_token');

        $this->versionRepository->method('fetchCurrent')
            ->willThrowException(new FailedToFetchVersion());

        $this->requestHandler->expects($this->once())
            ->method('handle');

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/', []);
        $sessionManager = new SessionMiddleware($this->tokenRepository, $this->versionRepository);
        $response = $sessionManager->process($request, $this->requestHandler);

        $this->assertNull($response->getHeaderLine('Location'));
    }
}