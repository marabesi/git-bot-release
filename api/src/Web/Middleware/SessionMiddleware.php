<?php
declare(strict_types=1);

namespace App\Web\Middleware;

use App\Domain\Gitlab\Authentication\TokenMiddlewareChecker;
use App\Domain\Gitlab\Authentication\TokenNotFound;
use App\Domain\Gitlab\Version\VersionRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Domain\Gitlab\Authentication\TokenRepository;
use Slim\Psr7\Response as SlimResponse;
use Exception;

class SessionMiddleware implements Middleware, TokenMiddlewareChecker
{

    const ALLOWED_ROUTES = [
        '/',
        '/request-token',
        '/unauthorized',
        '/hook/income',
        '/settings',
    ];

    private TokenRepository $gitlabRepository;
    private VersionRepository $versionRepository;

    public function __construct(
        TokenRepository $gitlabRepository,
        VersionRepository $versionRepository
    ) {
        $this->gitlabRepository = $gitlabRepository;
        $this->versionRepository = $versionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $token = $this->hasToken($request);

        if (!$token && !in_array($request->getUri()->getPath(), self::ALLOWED_ROUTES)) {
            return (new SlimResponse())
                ->withHeader('Location', '/');
        }

        try {
            if ($token) {
                $this->versionRepository->fetchCurrent();
            }
        } catch (Exception $error) {
            if (in_array($request->getUri()->getPath(), self::ALLOWED_ROUTES)) {
                return $handler->handle($request);
            }

            $this->gitlabRepository->deleteToken();

            return (new SlimResponse())
                ->withHeader('Location', '/');
        }

        return $handler->handle($request);
    }

    public function hasToken(Request $request): bool
    {
        try {
            if ($this->gitlabRepository->getToken()) {
                return true;
            }
        } catch (TokenNotFound $error) { }

        return false;
    }
}