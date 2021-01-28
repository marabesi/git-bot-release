<?php
declare(strict_types=1);

namespace App\Web\Actions\Gitlab\Webhook;

use App\Domain\ConventionalCommit\FindVersion;
use App\Domain\GenerateRelease\Assembly;
use App\Domain\GenerateRelease\Dispatch;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;
use App\Domain\Gitlab\Tag\TagRepository;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use App\Domain\Gitlab\File\FileRepository;
use App\Domain\Gitlab\Pipeline\PipelineRepository;
use App\Domain\Gitlab\Entity\Webhook;
use Exception;

class Income
{

    private Twig $twig;
    private FileRepository $fileRepository;
    private PipelineRepository $pipelineRepository;
    private TagRepository $tagRepository;
    private Webhook $webhook;
    private FilesToReleaseRepository $filesToReleaseRepository;

    public function __construct(
        Twig $twig,
        FileRepository $fileRepository,
        PipelineRepository $pipelineRepository,
        TagRepository $tagRepository,
        Webhook $webook,
        FilesToReleaseRepository $filesToReleaseRepository
    )
    {
        $this->twig = $twig;
        $this->fileRepository = $fileRepository;
        $this->pipelineRepository = $pipelineRepository;
        $this->tagRepository = $tagRepository;
        $this->webhook = $webook;
        $this->filesToReleaseRepository = $filesToReleaseRepository;
    }

    public function __invoke(Request $request, Response $response)
    {
        $incomeWebhookData = json_decode($request->getBody()->getContents(), true);

        $header = $request->getHeader('X-Gitlab-Token');
        $webhookSecret = '';

        if (count($header)) {
            $webhookSecret = $header[array_key_first($header)];
        }

        if ($webhookSecret !== $this->webhook->getToken()) {
            throw new Exception('Invalid webhook secret');
        }

        $ref = explode('/', $incomeWebhookData['ref']);
        $branch = $ref[array_key_last($ref)];

        $findVersion = new FindVersion($incomeWebhookData);

        $assemble = new Assembly($findVersion, $this->fileRepository, $branch, $this->filesToReleaseRepository);
        $assemble->setFilesToWriteRelease($this->filesToReleaseRepository->findAll($findVersion->getProjectId()));

        $release = $assemble->packVersion();

        $dispatch = new Dispatch($this->fileRepository);
        $dispatch->release($release);

        $tagName = sprintf('v%s', $findVersion->versionToRelease());

        $this->tagRepository->createTag($findVersion->getProjectId(), $tagName, $branch);

//        $this->pipelineRepository->trigger($findVersion->getProjectId(), $branch);

        return $response;
    }
}