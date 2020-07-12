<?php

namespace App\Domain\Gitlab;

use Psr\Http\Message\ServerRequestInterface;

interface TokenMiddlewareChecker
{

    public function hasToken(ServerRequestInterface $request): bool;

}