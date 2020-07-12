<?php

namespace App\Domain\Gitlab;

use App\Infrastructure\Gateway\NetworkRequest;

class GenerateToken
{

    public const OAUTH_TOKEN_URI = 'oauth/token';
    public const OAUTH_AUTHORIZE = 'oauth/authorize';

    private NetworkRequest $gitlabRequest;
    private Settings $settings;

    public function __construct(NetworkRequest $gitlabRequest, Settings $settings)
    {
        $this->gitlabRequest = $gitlabRequest;
        $this->settings = $settings;
    }

    public function requestToken(array $params): string
    {
        $response = $this->gitlabRequest->post(
            $this->settings->resolveGitlabUri(self::OAUTH_TOKEN_URI),
            $params
        );

        return $response['access_token'];
    }
}