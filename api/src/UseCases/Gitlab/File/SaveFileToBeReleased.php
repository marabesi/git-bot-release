<?php
declare(strict_types=1);

namespace App\UseCases\Gitlab\File;

use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;
use InvalidArgumentException;

class SaveFileToBeReleased
{

    private FilesToReleaseRepository $filesToReleaseRepository;

    public function __construct(FilesToReleaseRepository $filesToReleaseRepository)
    {
        $this->filesToReleaseRepository = $filesToReleaseRepository;
    }

    public function save(array $params)
    {
        if (count($params) < 2) {
            throw new InvalidArgumentException('Missing parameters, fullName, id');
        }

        $filePathAndName = $params['fullName'];

        $projectId = (int) $params['id'];

        if (!$projectId) {
            throw new InvalidArgumentException('project id should not be empty');
        }

        if (!$filePathAndName) {
            throw new InvalidArgumentException('fullName should not be empty');
        }

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
    }
}
