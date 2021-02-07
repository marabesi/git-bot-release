<?php

namespace Tests\Unit\UseCases\Gitlab\File;

use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;
use App\UseCases\Gitlab\File\DeleteFileToBeReleased;
use PHPUnit\Framework\TestCase;
use Exception;

class DeleteFileToBeReleasedTest extends TestCase
{

    public function test_delete_file()
    {
        $repository = $this->createMock(FilesToReleaseRepository::class);
        $repository->expects($this->once())
            ->method('delete')
            ->with(
                1,
                new File("10", '', '', '')
            )
            ->willReturn(true);

        $delete = new DeleteFileToBeReleased($repository);

        $this->assertTrue($delete->delete(1,10));
    }

    public function test_could_not_delete_file()
    {
        $this->expectException(Exception::class);

        $repository = $this->createMock(FilesToReleaseRepository::class);
        $repository->expects($this->once())
            ->method('delete')
            ->with(
                1,
                new File("10", '', '', '')
            )
            ->willReturn(false);

        $delete = new DeleteFileToBeReleased($repository);
        $delete->delete(1, 10);
    }
}
