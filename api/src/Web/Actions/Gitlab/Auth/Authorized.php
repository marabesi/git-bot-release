<?php

namespace App\Web\Actions\Gitlab\Auth;

use App\UseCases\Gitlab\ProjectList;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class Authorized
{

    private Twig $twig;
    private ProjectList $projectList;

    public function __construct(Twig $twig, ProjectList $projectList)
    {
        $this->twig = $twig;
        $this->projectList = $projectList;
    }

    public function __invoke(Request $request, Response $response)
    {
        return $this->twig->render($response,  'templates/authorized.twig',
            $this->projectList->fetch()
        );
    }
}
