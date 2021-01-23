<?php

namespace Tests\Domain\GenerateRelease;

use App\Domain\GenerateRelease\Dispatch;
use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\Entity\Release;
use App\Domain\Gitlab\File\FileRepository;
use PHPUnit\Framework\TestCase;

class DispatchTest extends TestCase
{

    private function makeRelease(array $files): Release
    {
        $release = new Release();
        $release->setBranch('main');
        $release->setFiles($files);
        $release->setMessage('chore: commited');
        $release->setProjectId(1);
        $release->setVersion('0.0.1');
        return $release;
    }

    public function test_update_array_of_files_given()
    {
        $release = $this->makeRelease([
            new File('1', 'file.txt', 'src/', '{}'),
        ]);

        $fileRepository = $this->createMock(FileRepository::class);
        $fileRepository->expects($this->once())
            ->method('bulkUpdate')
            ->with(1, 
                [
                    'actions' => [
                        [
                            'action' => 'update',
                            'file_path' => 'src/',
                            'content' => '{}'
                        ]
                    ]
                ],
                'main',
                'chore: updated files for release 0.0.1',
            );

        $dispatcher = new Dispatch($fileRepository);
        $dispatcher->release($release);
    }

    public function test_should_not_do_bulk_update_when_file_list_is_empty()
    {
        $release = $this->makeRelease([]);

        $fileRepository = $this->createMock(FileRepository::class);
        $fileRepository->expects($this->never())
            ->method('bulkUpdate');

        $dispatcher = new Dispatch($fileRepository);
        $dispatcher->release($release);
    }
}