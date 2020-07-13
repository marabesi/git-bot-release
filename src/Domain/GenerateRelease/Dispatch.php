<?php
declare(strict_types=1);

namespace App\Domain\GenerateRelease;

use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\Entity\Release;
use App\Domain\Gitlab\File\FileRepository;

class Dispatch
{

    private FileRepository $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function release(Release $release)
    {
        $actions = [];

        /**
         * @var $file File
         */
        foreach ($release->getFiles() as $file) {
            $actions['actions'][] = [
                'action' => 'update',
                'file_path' => $file->getPath(),
                'content' => $file->getContent(),
            ];
        }

        $this->fileRepository->bulkUpdate(
            $release->getProjectId(),
            $actions,
            $release->getBranch(),
            sprintf('chore: updated files for release %s', $release->getVersion()),
        );
    }
}