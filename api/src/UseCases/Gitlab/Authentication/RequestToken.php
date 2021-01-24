<?php
declare(strict_types=1);

namespace App\UseCases\Gitlab\Authentication;

use App\Domain\Gitlab\Authentication\GenerateToken;
use App\Domain\Gitlab\Entity\Settings;

class RequestToken
{
    private Settings $settings;

    public function __construct(
        Settings $settings
    ) {
        $this->settings = $settings;
    }

    public function url()
    {
        $url = $this->settings->resolveGitlabUri(GenerateToken::OAUTH_AUTHORIZE);
        return sprintf(
            '%s?client_id=%s&redirect_uri=%s&response_type=code&state=%s&scope=%s',
            $url,
            $this->settings->getClientId(),
            $this->settings->getRedirectUrl(),
            $this->settings->getState(),
            'read_repository+write_repository+api'
        );
    }
}
