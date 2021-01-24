<?php

namespace Tests\UseCases\Gitlab\File;

use App\Domain\Gitlab\Project\FilesToReleaseRepository;
use App\UseCases\Gitlab\File\SaveFileToBeReleased;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class SaveFileToBeReleasedTest extends TestCase
{

    public function test_parameters_given_are_empty()
    {
        $this->expectException(InvalidArgumentException::class);

        $filesRepository = $this->createMock(FilesToReleaseRepository::class);

        $useCase = new SaveFileToBeReleased($filesRepository);
        $useCase->save([]);
    }

    public function test_save_files_successfully()
    {
        $filesRepository = $this->createMock(FilesToReleaseRepository::class);
        $filesRepository->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $useCase = new SaveFileToBeReleased($filesRepository);
        $useCase->save([
            'id' => 1,
            'fullName' => 'src/index.json'
        ]);
    }
}
