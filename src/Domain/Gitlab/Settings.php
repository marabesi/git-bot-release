<?php

namespace App\Domain\Gitlab;

class Settings
{

    private string $gitlabUrl;
    private string $clientId;
    private string $secret;
    private string $redirectUrl;
    private string $state;

    public function __construct(string $gitlabUrl, string $clientId, string $secret, string $redirectUrl, string $state)
    {
        $this->gitlabUrl = $gitlabUrl;
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->redirectUrl = $redirectUrl;
        $this->state = $state;
    }

    public function getGitlabUrl(): string
    {
        return $this->gitlabUrl;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getGrantType(): string
    {
        return 'authorization_code';
    }

    public function resolveGitlabUri(string $uri): string
    {
        return sprintf('%s/%s', $this->getGitlabUrl(), $uri);
    }
}
