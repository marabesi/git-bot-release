<?php
declare(strict_types=1);

namespace App\Web\Middleware;

use App\Domain\Gitlab\Authentication\TokenMiddlewareChecker;
use App\Domain\Gitlab\Authentication\TokenNotFound;
use App\Domain\Gitlab\Authentication\TokenRevoked;
use App\Domain\Gitlab\Version\VersionRepository;
use App\Web\Routes;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Domain\Gitlab\Authentication\TokenRepository;
use Slim\Psr7\Response as SlimResponse;
use Exception;

class SessionMiddleware implements Middleware, TokenMiddlewareChecker
{

    private TokenRepository $tokenRepository;
    private VersionRepository $versionRepository;

    public function __construct(
        TokenRepository $gitlabRepository,
        VersionRepository $versionRepository
    ) {
        $this->tokenRepository = $gitlabRepository;
        $this->versionRepository = $versionRepository;
    }

    private function hasToken(Request $request): string
    {
        try {
            return $this->tokenRepository->getToken();
        } catch (TokenNotFound $error) { }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $token = $this->hasToken($request);
        $currentPath = $request->getUri()->getPath();

        if (!$token && !in_array($currentPath, Routes::getAllowedRoutes())) {
            return (new SlimResponse())
                ->withHeader('Location', '/');
        }

        try {
            if ($token) {
                $this->versionRepository->fetchCurrent();
            }
        } catch (TokenRevoked $tokenRevoked) {
            $this->tokenRepository->deleteToken();
        } catch (Exception $error) {
            if (in_array($currentPath, Routes::getAllowedRoutes())) {
                return $handler->handle($request);
            }

            return (new SlimResponse())
                ->withHeader('Location', '/');
        }

        return $handler->handle($request);
    }
}