<?php

namespace Tests\Unit\Domain\Gitlab;

use App\Domain\Gitlab\Entity\Settings;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{

    private Settings $settings;

    public function setUp(): void
    {
        parent::setUp();
        $this->settings = new Settings(
            'https://gitlab.com',
            'my-client-id',
            'my-secret',
            'https://iam-authenticated.com',
            'state-hash'
        );
    }

    public function test_define_gitlab_url()
    {
        $this->assertEquals($this->settings->getGitlabUrl(), 'https://gitlab.com');
    }

    public function test_glue_gitlab_url_and_uri_when_uri_doest_have_trailing_slash()
    {
        $this->assertEquals($this->settings->resolveGitlabUri('oauth/token'), 'https://gitlab.com/oauth/token');
    }
}