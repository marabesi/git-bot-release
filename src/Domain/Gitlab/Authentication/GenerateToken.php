<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Authentication;

use App\Infrastructure\Gateway\NetworkRequest;
use App\Domain\Gitlab\Entity\Settings;
use Exception;

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

        if (array_key_exists('error', $response)) {
            throw new Exception($response['error_description']);
        }

        return $response['access_token'];
    }
}