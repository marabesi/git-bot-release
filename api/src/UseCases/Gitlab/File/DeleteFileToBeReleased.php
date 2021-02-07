<?php
declare(strict_types=1);

namespace App\UseCases\Gitlab\File;

use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;
use Exception;

class DeleteFileToBeReleased
{

    private FilesToReleaseRepository $filesToReleaseRepository;

    public function __construct(FilesToReleaseRepository $filesToReleaseRepository)
    {
        $this->filesToReleaseRepository = $filesToReleaseRepository;
    }

    public function delete(int $projectId, int $fileId): bool
    {
        if (!$this->filesToReleaseRepository->delete(
            $projectId,
            new File("$fileId", '', '', ''),
        )) {
            throw new Exception('could not delete the file');
        }

        return true;
    }
}