<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class UnauthorizedTest extends TestCase
{
    use AppTest;

    public function test_show_link_to_try_again()
    {
        $response = $this->createRequest('GET', '/unauthorized');
        $this->assertStringContainsString('<a href="/">try again</a>', (string) $response->getBody());
    }
}
