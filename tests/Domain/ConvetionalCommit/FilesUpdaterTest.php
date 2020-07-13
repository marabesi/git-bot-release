<?php
declare(strict_types=1);

namespace Tests\Domain\ConvetionalCommit;

use App\Domain\DomainException\EmptyFile;
use App\Domain\DomainException\FileIsNotJson;
use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\Entity\Release;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;
use PHPUnit\Framework\TestCase;
use App\Domain\ConventionalCommit\FilesUpdater;

class FilesUpdaterTest extends TestCase
{

    public function test_not_allow_empty_files()
    {
        $this->expectException(EmptyFile::class);

        $filesToUpdate = [
            new File('1', 'myfile', '', ''),
        ];

        $release = new Release();
        $release->setProjectId(1);
        $release->setVersion('1.0.0');
        $release->setBranch('master');

        $filesToReleaseRepository = $this->createStub(FilesToReleaseRepository::class);

        $updater = new FilesUpdater($filesToUpdate, $release, $filesToReleaseRepository);

        $updater->makeRelease();
    }

    public function test_ignore_files_that_are_not_json()
    {
        $this->expectException(FileIsNotJson::class);

        $filesToUpdate = [
            new File('111', 'myfile', 'frontend/mytext.txt', 'random file'),
        ];

        $release = new Release();
        $release->setProjectId(1);
        $release->setVersion('1.0.0');
        $release->setBranch('master');

        $filesToReleaseRepository = $this->createStub(FilesToReleaseRepository::class);

        $updater = new FilesUpdater($filesToUpdate, $release, $filesToReleaseRepository);

        $updater->makeRelease();
    }

    public function test_update_files_with_new_release_version()
    {
        $filesToUpdate = [
            new File('111', 'myfile', 'frontend/package.json', '{"version":""}'),
        ];

        $release = new Release();
        $release->setProjectId(1);
        $release->setVersion('1.0.0');
        $release->setBranch('master');

        $filesToReleaseRepository = $this->createStub(FilesToReleaseRepository::class);

        $updater = new FilesUpdater($filesToUpdate, $release, $filesToReleaseRepository);

        $releaseFiles = $updater->makeRelease();

        $this->assertCount(1, $releaseFiles);
    }

    public function test_indent_package_json_using_two_spaces_separator()
    {
        $filesToUpdate = [
            new File('111', 'package.json', 'frontend/mytext.txt', '{}'),
        ];

        $release = new Release();
        $release->setProjectId(1);
        $release->setVersion('1.0.0');
        $release->setBranch('master');

        $filesToReleaseRepository = $this->createStub(FilesToReleaseRepository::class);

        $updater = new FilesUpdater($filesToUpdate, $release, $filesToReleaseRepository);

        $files = $updater->makeRelease();

        $twoSpaceIdentation =<<<JSON
{
  "version": "1.0.0"
}
JSON;

        $this->assertEquals($twoSpaceIdentation, $files[0]->getContent());
    }
}