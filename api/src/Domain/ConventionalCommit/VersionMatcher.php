<?php
declare(strict_types=1);

namespace App\Domain\ConventionalCommit;

class VersionMatcher
{

    public const CONVENTIONAL_COMMIT_MATCHER = '/chore: tagged version ([-0-9]+\.[-0-9]+\.[-0-9]+)/';
    private string $toMatch;

    public function __construct(string $toMatch)
    {
        $this->toMatch = $toMatch;
    }

    public function match(): string
    {
        $matches = [];
        $match = preg_match(self::CONVENTIONAL_COMMIT_MATCHER, $this->toMatch, $matches);

        if (count($matches) < 2) {
            return '';
        }

        return $matches[1];
    }
}