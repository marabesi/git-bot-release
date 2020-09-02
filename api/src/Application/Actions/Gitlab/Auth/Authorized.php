<?php

namespace App\Application\Actions\Gitlab\Auth;

use App\Domain\Gitlab\Project\ProjectsRepository;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class Authorized
{

    private Twig $twig;
    private ProjectsRepository $projectsRepository;

    public function __construct(Twig $twig, ProjectsRepository $projectsRepository)
    {
        $this->twig = $twig;
        $this->projectsRepository = $projectsRepository;
    }

    public function __invoke(Request $request, Response $response)
    {
        return $this->twig->render($response,  'templates/authorized.twig',
            [ 'projects' => $this->projectsRepository->fetchProjects() ]
        );
    }
}
