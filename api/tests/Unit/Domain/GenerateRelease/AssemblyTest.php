<?php

namespace Tests\Unit\Domain\GenerateRelease;

use App\Domain\ConventionalCommit\FindVersion;
use App\Domain\DomainException\NoFilesToRelease;
use App\Domain\GenerateRelease\Assembly;
use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\Entity\Release;
use App\Domain\Gitlab\File\FileRepository;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;
use PHPUnit\Framework\TestCase;

class AssemblyTest extends TestCase
{

    private FindVersion $findVersion;

    public function setUp(): void
    {
        parent::setUp();
        $webhookPushJson = json_decode(file_get_contents(realpath(__DIR__ . '/../../../../var/mock/webhook_push_with_version.json')), true);
        $this->findVersion = new FindVersion($webhookPushJson);
    }


    public function test_should_throw_error_if_no_files_are_found_to_release()
    {
        $this->expectException(NoFilesToRelease::class);

        $fileRepository = $this->createStub(FileRepository::class);
        $branch = 'master';
        $filesToReleaseRepository = $this->createStub(FilesToReleaseRepository::class);

        $assembler = new Assembly($this->findVersion, $fileRepository, $branch, $filesToReleaseRepository);
        $assembler->setFilesToWriteRelease([]);

        $release = $assembler->packVersion(new Release());

        $this->assertEquals('0.0.12', $release->getVersion());
    }

    public function test_should_generate_new_version()
    {
        $file = new File('1', 'myfile.json', 'src/myfile.json', '{}');

        $fileRepository = $this->createMock(FileRepository::class);
        $fileRepository->method('findFile')
            ->willReturn(
                $file
            );
        $branch = 'master';
        $filesToReleaseRepository = $this->createStub(FilesToReleaseRepository::class);

        $assembler = new Assembly($this->findVersion, $fileRepository, $branch, $filesToReleaseRepository);
        $assembler->setFilesToWriteRelease([$file]);

        $release = $assembler->packVersion(new Release());

        $this->assertEquals('0.0.12', $release->getVersion());
    }
}