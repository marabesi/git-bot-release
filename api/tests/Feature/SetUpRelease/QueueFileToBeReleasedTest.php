<?php
declare(strict_types=1);

namespace Tests\Feature\SetUpRelease;

use App\Domain\Gitlab\Authentication\TokenRepository;
use App\Domain\Gitlab\Version\VersionRepository;
use Tests\Feature\AppTest;
use Tests\Feature\Stubs\WithFakeToken;
use Tests\Feature\Stubs\WithFakeVersion;

class QueueFileToBeReleasedTest extends AppTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->container->set(TokenRepository::class, new WithFakeToken());
        $this->container->set(VersionRepository::class, new WithFakeVersion());
    }

    public function invalidRequests(): array
    {
        return [
            [ [] ],
            [ [ 'fullName' => '' ] ],
        ];
    }

    public function test_save_file()
    {
        $response = $this->post('/projects/1/file', [
            'fullName' => 'src/test.json'
        ]);

        $this->assertStringContainsString('/projects/1/detail', $response->getHeaderLine('Location'));
    }

    /**
     * @dataProvider invalidRequests
     */
    public function test_invalid_requests($request)
    {
        $response = $this->post('/projects/1/file', $request);

        $this->assertEquals(400, $response->getStatusCode(), 'request body is valid, it should be invalid');
    }

    public function test_invalid_project_id()
    {
        $response = $this->post('/projects/0/file', [
            'fullName' => 'myfile.txt'
        ]);

        $this->assertEquals(400, $response->getStatusCode(), 'The project id given is valid, it should be invalid');
    }
}