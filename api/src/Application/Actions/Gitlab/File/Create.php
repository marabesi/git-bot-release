<?php
declare(strict_types=1);

namespace App\Application\Actions\Gitlab\File;

use App\Domain\Gitlab\Entity\File;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;

class Create
{

    private FilesToReleaseRepository $filesToReleaseRepository;

    public function __construct(FilesToReleaseRepository $filesToReleaseRepository)
    {
        $this->filesToReleaseRepository = $filesToReleaseRepository;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $params = $request->getParsedBody();

        $filePathAndName = $params['fullName'];

        $projectId = (int) $args['id'];

        $fileMetadata = explode('/', $filePathAndName);

        $path = '';

        for ($i = 0; $i < count($fileMetadata) - 1; $i++) {
            $path .= $fileMetadata[$i] . '/';
        }

        $this->filesToReleaseRepository->save(
            $projectId,
            new File(
                uniqid($filePathAndName),
                $fileMetadata[count($fileMetadata) - 1],
                $path,
                ''
            )
        );

        return $response
            ->withHeader('Location', '/projects/' . $args['id'] . '/detail');
    }
}