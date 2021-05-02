<?php
declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Persistence\Gitlab;

use App\Domain\Gitlab\Authentication\TokenNotFound;
use App\Infrastructure\Persistence\Gitlab\TokenFilesystemRepository;
use Tests\Feature\AppTest;

class TokenFilesystemRepositoryIntegrationTest extends AppTest
{

    private TokenFilesystemRepository $repository;

    public function test_store_token_in_the_file_system()
    {
        $this->repository = $this->container->get(TokenFilesystemRepository::class);
        $this->assertTrue($this->repository->storeToken('my_token'));
    }

    public function test_retrieve_previously_stored_token()
    {
        $token = 'previous_token';
        $this->repository = $this->container->get(TokenFilesystemRepository::class);

        $this->repository->storeToken($token);
        $fetchToken = $this->repository->getToken();

        $this->assertEquals($token, $fetchToken);
    }

    public function test_error_when_trying_to_retrieve_token_that_does_not_exists()
    {
        $this->expectException(TokenNotFound::class);

        $this->repository = $this->container->get(TokenFilesystemRepository::class);
        $this->repository->deleteToken();

        $this->repository->getToken();
    }

    public function test_replace_previous_token_with_new_token()
    {
        $this->repository = $this->container->get(TokenFilesystemRepository::class);

        $this->repository->storeToken('previous_token');
        $this->repository->storeToken('newest_token');

        $this->assertEquals('newest_token', $this->repository->getToken());
    }
}