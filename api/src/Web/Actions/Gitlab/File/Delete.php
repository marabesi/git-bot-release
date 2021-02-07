<?php
declare(strict_types=1);

namespace App\Web\Actions\Gitlab\File;

use App\UseCases\Gitlab\File\DeleteFileToBeReleased;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Delete
{

    private DeleteFileToBeReleased $deleteFileToBeReleased;

    public function __construct(DeleteFileToBeReleased $deleteFileToBeReleased)
    {
        $this->deleteFileToBeReleased = $deleteFileToBeReleased;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $projectId = (int) $args['id'];
        $fileId = (int) $args['fileId'];

        $this->deleteFileToBeReleased->delete(
            $projectId,
            $fileId
        );

        return $response
            ->withHeader('Location', '/projects/' . $projectId . '/detail');
    }
}