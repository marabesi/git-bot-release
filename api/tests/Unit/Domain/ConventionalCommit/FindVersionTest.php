<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\ConventionalCommit;

use App\Domain\DomainException\VersionToReleaseNotFound;
use PHPUnit\Framework\TestCase;
use App\Domain\ConventionalCommit\FindVersion;

class FindVersionTest extends TestCase
{

    private FindVersion $generateVersion;
    private array $webhookPushJson = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->webhookPushJson = json_decode(file_get_contents(realpath(__DIR__ . '/../../../../var/mock/webhook_push_no_version.json')), true);
        $this->generateVersion = new FindVersion($this->webhookPushJson);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->webhookPushJson = [];
    }

    public function test_extract_project_id_from_webhook_response()
    {
        $this->assertEquals('19762552', $this->generateVersion->getProjectId());
    }

    public function test_should_do_nothing_when_the_commit_list_has_no_version_to_generate()
    {
        $this->expectException(VersionToReleaseNotFound::class);
        $this->generateVersion->versionToRelease();
    }

    public function test_should_generate_version_based_on_commit_title()
    {
        $webhookPushJson = json_decode(file_get_contents(realpath(__DIR__ . '/../../../../var/mock/webhook_push_with_version.json')), true);
        $generateVersion = new FindVersion($webhookPushJson);
        $this->assertEquals('0.0.12', $generateVersion->versionToRelease());
    }
}