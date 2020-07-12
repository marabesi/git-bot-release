<?php

namespace Gitbot\Gitlab;

use Gitbot\Infrastructure\NetworkRequestAuthenticated;

class File
{
    private NetworkRequestAuthenticated $networkRequest;

    public function __construct(NetworkRequestAuthenticated $networkRequest)
    {
        $this->networkRequest = $networkRequest;
    }

    public function update()
    {
        $projectId = '19762552';
        $url = sprintf('https://gitlab.com/api/v4/projects/%s/repository/files/%s', $projectId, 'README.md');

        $this->networkRequest->put($url, [
            'branch' => 'master',
            'content' => 'updated via api',
            'commit_message' => 'content updated via api'
        ]);
    }
}