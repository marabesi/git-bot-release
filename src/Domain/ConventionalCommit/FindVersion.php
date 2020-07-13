<?php
declare(strict_types=1);

namespace App\Domain\ConventionalCommit;

use App\Domain\DomainException\VersionToReleaseNotFound;

class FindVersion
{
    private array $webhookPushContent;

    public function __construct(array $webhookPushContent)
    {
        $this->webhookPushContent = $webhookPushContent;
    }

    public function getProjectId(): int
    {
        return (int) $this->webhookPushContent['project_id'];
    }

    public function versionToRelease(): string
    {
        foreach ($this->webhookPushContent['commits'] as $commit) {
            $matcher = new VersionMatcher($commit['title']);
            $version = $matcher->match();

            if ($version !== '') {
                return $version;
            }
        }

        throw new VersionToReleaseNotFound();
    }
}