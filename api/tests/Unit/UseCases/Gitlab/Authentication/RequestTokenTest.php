<?php

namespace Tests\Unit\UseCases\Gitlab\Authentication;

use App\Domain\Gitlab\Entity\Settings;
use App\UseCases\Gitlab\Authentication\RequestToken;
use PHPUnit\Framework\TestCase;

class RequestTokenTest extends TestCase
{

    private RequestToken $requestToken;

    public function setUp(): void
    {
        parent::setUp();

        $settings = new Settings(
            'http://mygitlab.com',
            '123',
            'secret',
            'http://localhost',
            'STATE',
            );
        $this->requestToken = new RequestToken($settings);
    }

    public function test_uses_client_id_to_generate_token_url()
    {
        $this->assertStringContainsString('client_id=123', $this->requestToken->url());
    }

    public function test_uses_redirect_url_to_generate_token_url()
    {
        $this->assertStringContainsString('redirect_uri=http://localhost', $this->requestToken->url());
    }

    public function test_hard_code_response_type_to_be_code()
    {
        $this->assertStringContainsString('response_type=code', $this->requestToken->url());
    }

    public function test_uses_state_to_generate_token_url()
    {
        $this->assertStringContainsString('state=STATE', $this->requestToken->url());
    }

    public function test_hard_code_scope()
    {
        $this->assertStringContainsString('scope=read_repository+write_repository+api', $this->requestToken->url());
    }
}