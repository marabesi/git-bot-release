<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Entity;

class Webhook
{

    private string $url;
    private string $token;
    private bool $pushEvents;
    private bool $enableSslVerification;

    public function __construct(
        string $url = '',
        string $token = '',
        bool $pushEvents = false,
        bool $enableSslVerification = false
    ) {
        $this->url = $url;
        $this->token = $token;
        $this->pushEvents = $pushEvents;
        $this->enableSslVerification = $enableSslVerification;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function getPushEvents()
    {
        return $this->pushEvents;
    }

    public function setPushEvents($pushEvents)
    {
        $this->pushEvents = $pushEvents;
        return $this;
    }

    public function getEnableSslVerification()
    {
        return $this->enableSslVerification;
    }

    public function setEnableSslVerification($enableSslVerification)
    {
        $this->enableSslVerification = $enableSslVerification;
        return $this;
    }
}