<?php

namespace Tests\Unit\Infrastructure\Gateway\Gitlab;

use App\Infrastructure\Gateway\Gitlab\PipelineApiRepository;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use PHPUnit\Framework\TestCase;

class PipelineApiRepositoryTest extends TestCase
{

    public function test_triggers_pipeline()
    {
        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('post')
            ->willReturn([ 'id' => 123 ]);

        $repository = new PipelineApiRepository($authenticatedRequest);
        $response = $repository->trigger(
            1,
            'main',
        );

        $this->assertTrue($response);
    }
}