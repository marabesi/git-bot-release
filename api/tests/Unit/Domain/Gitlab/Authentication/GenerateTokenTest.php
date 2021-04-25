<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Gitlab;

use App\Domain\Gitlab\Authentication\GenerateToken;
use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Project\SettingsRepository;
use App\Infrastructure\Gateway\NetworkRequest;
use Exception;
use PHPUnit\Framework\TestCase;

class GenerateTokenTest extends TestCase
{

    private Settings $settings;

    public function setUp(): void
    {
        parent::setUp();

        $this->settings = new Settings(
        'http://gitlab.com',
        '123',
        '4444',
        'http://localhost',
        'test',
    );
    }

    public function test_request_access_token_to_gitlab()
    {
        $network = $this->createMock(NetworkRequest::class);
        $network->expects($this->once())
            ->method('post')
            ->willReturn(
                [
                    'access_token' => '123'
                ]
            );

        $settingsRepository = $this->createMock(SettingsRepository::class);
        $settingsRepository->expects($this->once())
            ->method('get')
            ->willReturn($this->settings);

       $generateToken = new GenerateToken($network, $settingsRepository);

       $this->assertEquals('123', $generateToken->requestToken([]));
    }

    public function test_throw_exception_when_token_generation_fails()
    {
        $this->expectException(Exception::class);
        $network = $this->createMock(NetworkRequest::class);
        $network->expects($this->once())
            ->method('post')
            ->willReturn(
                [
                    'error' => 9191,
                    'error_description' => 'something went wrong'
                ]
            );

        $settingsRepository = $this->createMock(SettingsRepository::class);
        $settingsRepository->expects($this->once())
            ->method('get')
            ->willReturn($this->settings);

        $generateToken = new GenerateToken($network, $settingsRepository);
        $generateToken->requestToken([]);
    }
}