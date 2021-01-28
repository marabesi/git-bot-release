<?php
declare(strict_types=1);

namespace App\Infrastructure\Gateway\Gitlab;

use App\Domain\Gitlab\Pipeline\PipelineRepository;
use App\Infrastructure\Gateway\Gitlab\Exception\FailedToCreateTag;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;

class PipelineApiRepository implements PipelineRepository
{
    private NetworkRequestAuthenticated $networkRequestAuthenticated;

    public function __construct(NetworkRequestAuthenticated $networkRequestAuthenticated)
    {
       $this->networkRequestAuthenticated = $networkRequestAuthenticated;
    }

    public function trigger(int $projectId, string $branch, array $variables = []): bool
    {
        $url = sprintf('api/v4/projects/%s/pipeline', $projectId);

        $variables['ref'] = $branch;

        $response = $this->networkRequestAuthenticated->post($url, $variables);

        if (array_key_exists('id', $response)) {
            return true;
        }

        throw new FailedToCreateTag($response['message']['base'][0]);
    }
}