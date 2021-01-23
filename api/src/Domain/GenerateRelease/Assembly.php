<?php
declare(strict_types=1);

namespace App\Domain\GenerateRelease;

use App\Domain\ConventionalCommit\FilesUpdater;
use App\Domain\ConventionalCommit\FindVersion;
use App\Domain\DomainException\NoFilesToRelease;
use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\Entity\Release;
use App\Domain\Gitlab\File\FileRepository;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;

class Assembly
{

    private FindVersion $findVersion;
    private FileRepository $fileRepository;
    private array $filesToWriteRelease;
    private string $branchName;
    private FilesToReleaseRepository $filesToReleaseRepository;

    public function __construct(
        FindVersion $findVersion,
        FileRepository $fileRepository,
        string $branchName,
        FilesToReleaseRepository $filesToReleaseRepository
    ) {
        $this->findVersion = $findVersion;
        $this->fileRepository = $fileRepository;
        $this->branchName = $branchName;
        $this->filesToReleaseRepository = $filesToReleaseRepository;
    }

    public function getFilesToWriteRelease(): array
    {
        return $this->filesToWriteRelease;
    }

    public function setFilesToWriteRelease(array $filesToWriteRelease)
    {
        $this->filesToWriteRelease = $filesToWriteRelease;
        return $this;
    }

    public function packVersion(): Release
    {
        $filesToRelease = $this->getFilesToWriteRelease();

        if (count($filesToRelease) === 0) {
            throw new NoFilesToRelease();
        }

        $files = [];

        /** @var File $file */
        foreach ($filesToRelease as $file) {
            $files[] = $this->fileRepository->findFile(
                $this->findVersion->getProjectId(),
                sprintf('%s%s', $file->getPath(), $file->getName()),
                $this->branchName
            );
        }

        $versionToRelease = $this->findVersion->versionToRelease();

        $release = new Release();
        $release->setProjectId($this->findVersion->getProjectId());
        $release->setBranch($this->branchName);
        $release->setVersion($versionToRelease);

        $fileUpdater = new FilesUpdater($files, $release, $this->filesToReleaseRepository);
        $filesToRelease = $fileUpdater->makeRelease();

        $release->setFiles($filesToRelease);

        return $release;
    }
}