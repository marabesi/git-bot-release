<?php
declare(strict_types=1);

namespace Tests\Domain\ConvetionalCommit;

use App\Domain\ConventionalCommit\VersionMatcher;
use PHPUnit\Framework\TestCase;

class VersionMatcherTest extends TestCase
{

    public function test_should_return_empty_when_there_is_no_match()
    {
        $matcher = new VersionMatcher('');
        $this->assertEquals('', $matcher->match());
    }

    public function commitMessageWithNoMatches()
    {
        return [
            ['chore'],
            ['chore: '],
        ];
    }

    /**
     * @dataProvider commitMessageWithNoMatches
     */
    public function test_should_not_match_if_the_string_has_no_conventional_pattern(string $message)
    {
        $matcher = new VersionMatcher($message);
        $this->assertEquals('', $matcher->match());
    }

    public function commitMessageWithMatches()
    {
        return [
            ['chore: tagged version 1.0.0', '1.0.0']
        ];
    }

    /**
     * @dataProvider commitMessageWithMatches
     */
    public function test_should__match_if_the_string_has_conventional_pattern(string $message, string $version)
    {
        $matcher = new VersionMatcher($message);
        $this->assertEquals($version, $matcher->match());
    }
}