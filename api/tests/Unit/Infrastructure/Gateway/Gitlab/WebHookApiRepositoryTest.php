<?php

namespace Tests\Unit\Infrastructure\Gateway\Gitlab;

use App\Domain\Gitlab\Entity\Webhook;
use App\Infrastructure\Gateway\Gitlab\WebHookApiRepository;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use PHPUnit\Framework\TestCase;

class WebHookApiRepositoryTest extends TestCase
{

    public function test_delete_webhook_from_project()
    {
        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('delete')
            ->willReturn([]);
        $repository = new WebHookApiRepository($authenticatedRequest);
        $repository->deleteWebhook(
            1,
            1,
        );
    }

    public function test_register_webhook_for_a_project()
    {
        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('post')
            ->willReturn([
                'message' => 'webhook created',
                'id' => 1,
            ]);

        $webhook = new Webhook('http://my.hook', '123', true, false);

        $repository = new WebHookApiRepository($authenticatedRequest);
        $response = $repository->registerWebhook(
            1,
            $webhook
        );

        $this->assertTrue($response, 'Could not register webook');
    }
}