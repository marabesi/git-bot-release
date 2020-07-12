<?php
declare(strict_types=1);

namespace App\Application\Middleware;

use App\Domain\Gitlab\TokenMiddlewareChecker;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Domain\Gitlab\GitlabRepository;

class SessionMiddleware implements Middleware, TokenMiddlewareChecker
{

    private GitlabRepository $gitlabRepository;

    public function __construct(GitlabRepository $gitlabRepository)
    {
        $this->gitlabRepository = $gitlabRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $this->hasToken($request);
        return $handler->handle($request);
    }

    public function hasToken(Request $request): bool
    {
        $token = $this->gitlabRepository->getToken();

        return false;
    }
}