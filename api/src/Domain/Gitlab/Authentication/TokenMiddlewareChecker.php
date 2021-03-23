<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Authentication;

use Psr\Http\Message\ServerRequestInterface;

interface TokenMiddlewareChecker
{

    public function hasToken(ServerRequestInterface $request): string;

}