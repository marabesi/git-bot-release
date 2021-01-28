<?php
declare(strict_types=1);

namespace App\Infrastructure\Gateway\Gitlab;

use App\Domain\Gitlab\Tag\TagRepository;
use App\Infrastructure\Gateway\Gitlab\Exception\FailedToCreateTag;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;

class TagApiRepository implements TagRepository
{
    private NetworkRequestAuthenticated $networkRequestAuthenticated;

    public function __construct(NetworkRequestAuthenticated $networkRequestAuthenticated)
    {
        $this->networkRequestAuthenticated = $networkRequestAuthenticated;
    }

    public function createTag(int $projectId, string $name, string $fromBranch)
    {
        $url = sprintf('api/v4/projects/%s/repository/tags', $projectId);

        $response = $this->networkRequestAuthenticated->post($url, [
            'tag_name' => $name,
            'ref' => $fromBranch,
        ]);

        if (array_key_exists('commit', $response)) {
            return true;
        }

        throw new FailedToCreateTag($response['message']);
    }
}