<?php

namespace Tests\Integration\Settings;

use PHPUnit\Framework\TestCase;
use Tests\Integration\AppTest;

class SaveNewSettingTest extends TestCase
{

    use AppTest;

    public function test_should_ask_for_setup_settings_if_it_not_exists()
    {
        $response = $this->createRequest('GET', '/');
        $this->assertStringContainsString('/request-token', $response->getBody());
    }

    public function test_save_settings()
    {
        $response = $this->createRequest('POST', '/settings', [
            'gitlab_url' => 'asdasd.coa',
            'client_id' => 'asasdasd',
            'secret' => 'asdasd',
            'redirect_url' => 'asdasdasd.cc',
            'state' => '1111',
        ]);

        $this->assertTrue(
            $response->hasHeader('Location')
        );
    }
}