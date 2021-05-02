<?php
declare(strict_types=1);

namespace Tests\Feature\Project;

use App\Domain\Gitlab\Authentication\TokenRepository;
use App\Domain\Gitlab\Entity\Webhook;
use App\Domain\Gitlab\Project\ProjectsRepository;
use App\Domain\Gitlab\Version\VersionRepository;
use Tests\Feature\AppTest;
use Tests\Feature\Stubs\WithFakeProjects;
use Tests\Feature\Stubs\WithFakeToken;
use Tests\Feature\Stubs\WithFakeVersion;

class PrintDetailsTest extends AppTest
{

    public function setUp(): void
    {
        parent::setUp();
        $this->container->set(TokenRepository::class, new WithFakeToken());
        $this->container->set(VersionRepository::class, new WithFakeVersion());
    }

    public function test_print_project_name_and_description()
    {
        $this->container->set(ProjectsRepository::class, new WithFakeProjects());

        $response = $this->get('/projects/1/detail');
        $body =  (string) $response->getBody();

        $this->assertStringContainsString('<h1>project 1</h1>', $body, 'Could not find the project title');
        $this->assertStringContainsString('<p>my project description</p>', $body, 'Could not find the project description');
    }

    public function test_print_project_without_webhook_disabled()
    {
        $this->container->set(ProjectsRepository::class, new WithFakeProjects());

        $response = $this->get('/projects/1/detail');
        $body =  (string) $response->getBody();

        $this->assertStringContainsString('Enable deploy via conventional commit', $body, 'The project has the webhook enabled');
    }

    public function test_print_project_with_webhook_active()
    {
        $webhook = new Webhook(WithFakeProjects::WEBHOOK_URL);

        $this->container->set(Webhook::class, $webhook);
        $this->container->set(ProjectsRepository::class, new WithFakeProjects());

        $response = $this->get('/projects/1/detail');
        $body =  (string) $response->getBody();

        $this->assertStringContainsString('<p>Hook for deploying with conventional commit already registered! Enjoy!</p>', $body, 'It seems the projects does not have the webhook in place');
    }
}
