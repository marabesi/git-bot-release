<?php
declare(strict_types=1);

namespace App\Infrastructure\Gateway\Gitlab;

use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\File\FileRepository;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use Exception;

class FileApiRepository implements FileRepository
{

    private NetworkRequestAuthenticated $networkRequestAuthenticated;

    public function __construct(NetworkRequestAuthenticated $networkRequestAuthenticated)
    {
        $this->networkRequestAuthenticated = $networkRequestAuthenticated;
    }

    public function update(int $projectId, string $file, array $params): array
    {
        $url = sprintf('api/v4/projects/%s/repository/files/%s', $projectId, $file);

        $updatedFile = $this->networkRequestAuthenticated->put($url, $params);

        if (array_key_exists('message', $updatedFile)) {
            throw new Exception($updatedFile['message']);
        }

        return $updatedFile;
    }

    public function findFile(int $projectId, string $fileAndPath, string $branchName): File
    {
        $url = sprintf('api/v4/projects/%s/repository/files/%s', $projectId, $fileAndPath);

        $response = $this->networkRequestAuthenticated->get($url, [
            'ref' => $branchName,
        ]);

        if (array_key_exists('message', $response)) {
            throw new Exception($response['message']);
        }

        if (array_key_exists('error', $response)) {
            throw new Exception($response['error'] . ': ' . $fileAndPath . ' project id: ' . $projectId);
        }

        return new File(
            $response['blob_id'],
            $response['file_name'],
            $response['file_path'],
            base64_decode($response['content'])
        );
    }

    public function bulkUpdate(int $projectId, array $files, string $branchName, string $commitMessage): array
    {
        $url = sprintf('api/v4/projects/%s/repository/commits', $projectId);

        if (!array_key_exists('actions', $files)) {
            throw new Exception('There are no files to release the version:' . $commitMessage);
        }

        $response = $this->networkRequestAuthenticated->post($url, [
            'branch' => $branchName,
            'commit_message'=> $commitMessage,
            'actions' => $files['actions'],
        ]);

        if (array_key_exists('error', $response)) {
            throw new Exception($response['error']);
        }

        return $response;
    }
}