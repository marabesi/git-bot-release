<?php

namespace Tests\Feature;

class UnauthorizedTest extends AppTest
{

    public function test_show_link_to_try_again()
    {
        $response = $this->createRequest('GET', '/unauthorized');
        $this->assertStringContainsString('<a href="/">try again</a>', (string) $response->getBody());
    }
}
