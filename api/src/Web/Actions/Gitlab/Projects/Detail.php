<?php
declare(strict_types=1);

namespace App\Web\Actions\Gitlab\Projects;

use App\UseCases\Gitlab\Project\Details;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class Detail
{

    private Twig $twig;
    private Details $details;

    public function __construct(
        Twig $twig,
        Details $details
    )
    {
        $this->details = $details;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $projectId = (int) $args['id'];

        return $this->twig->render(
            $response,
            'templates/projects/detail.twig',
            $this->details->fetchForProject($projectId)
        );
    }
}