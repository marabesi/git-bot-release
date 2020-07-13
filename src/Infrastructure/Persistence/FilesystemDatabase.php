<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;
use Filebase\Database;
use Filebase\Document;
use Exception;

class FilesystemDatabase implements FilesToReleaseRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    private function generateId(string $path, string $name, int $projectId): string
    {
        return $projectId . base64_encode(sprintf('%s%s', $path, $name));
    }

    public function save(int $projectId, File $file): bool
    {
        $document = new Document($this->database);
        $document->setId($file->getId());
        $document->set([
            'projectId' => $projectId,
            'path' => $file->getPath(),
            'name' => $file->getName(),
        ]);

        if ($this->database->save($document)) {
            return true;
        }

        throw new Exception('error while saving');
    }

    public function delete(int $projectId, File $file): bool
    {
        $document = $this->database->get($file->getId());

        if ($this->database->delete($document)) {
            return true;
        }

        throw new Exception('error trying to delete file');
    }

    public function findAll(int $projectId): array
    {
        $files = [];
        $documents = $this->database->query()
            ->where(['projectId' => $projectId])
            ->resultDocuments();

        /** @var Document $document */
        foreach ($documents as $document) {
            $data = $document->getData();
            $files[] = new File(
                (string) $document->getId(),
                (string) $data['name'],
                (string) $data['path'],
                ''
            );
        }

        return $files;
    }
}