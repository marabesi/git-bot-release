<?php

namespace App\Application\Actions\Gitlab\Auth;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class Unauthorized
{

    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response)
    {
        return $this->twig->render($response,  'templates/unauthorized.twig');
    }
}
