<?php
declare(strict_types=1);

namespace Tests\Feature\Stubs;

use App\Domain\Gitlab\Project\ProjectsRepository;

class WithFakeProjects implements ProjectsRepository
{

    const WEBHOOK_URL = 'http://fake-webhook.com/income/message';

    public function fetchProjects(): array
    {
        return [];
    }

    public function fetchProject(int $id): array
    {
        return [
            'id' => '123',
            'name_with_namespace' => 'project 1',
            'description' => 'my project description',
            'web_url' => '',
        ];
    }

    public function fetchWebhooks(int $id): array
    {
        return [
            [
                'url' => self::WEBHOOK_URL
            ]
        ];
    }
}
