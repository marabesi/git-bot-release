<?php

namespace Gitbot\Gitlab;

use Gitbot\Infrastructure\NetworkRequestAuthenticated;

class Webhook
{
    private NetworkRequestAuthenticated $networkRequest;

    public function __construct(NetworkRequestAuthenticated $networkRequest)
    {
        $this->networkRequest = $networkRequest;
    }

    public function register()
    {
        $projectId = '19762552';
        $url = sprintf('https://gitlab.com/api/v4/projects/%s/hooks', $projectId);

        $this->networkRequest->post(
            $url,
            [
                'url' => 'https://9f55d5fc89ad.ngrok.io/webhook.php',
                'token' =>  1213123123,
                'push_events' => true,
                'enable_ssl_verification' => true,
            ]
        );
    }
}