<?php
declare(strict_types=1);

namespace App\Web\Actions\Gitlab\File;

use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Delete
{

    private FilesToReleaseRepository $filesToReleaseRepository;

    public function __construct(FilesToReleaseRepository $filesToReleaseRepository)
    {
        $this->filesToReleaseRepository = $filesToReleaseRepository;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $this->filesToReleaseRepository->delete(
            (int) $args['id'],
            new File($args['fileId'], '', '', ''),
        );

        return $response
            ->withHeader('Location', '/projects/' . $args['id'] . '/detail');
    }
}