<?php
declare(strict_types=1);

namespace App\Web\Actions\Gitlab\File;

use App\UseCases\Gitlab\File\SaveFileToBeReleased;
use InvalidArgumentException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Create
{

    private SaveFileToBeReleased $saveFileToBeReleased;

    public function __construct(SaveFileToBeReleased $saveFileToBeReleased)
    {
        $this->saveFileToBeReleased = $saveFileToBeReleased;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        try {
        $params = $request->getParsedBody();
        $projectId = (int) $args['id'];

        $params['id'] = $projectId;

        $this->saveFileToBeReleased->save($params);

        return $response
            ->withHeader('Location', '/projects/' . $projectId . '/detail');
        } catch (InvalidArgumentException $error) {
           return $response
               ->withStatus(400);
        }
    }
}