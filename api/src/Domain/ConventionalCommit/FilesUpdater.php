<?php
declare(strict_types=1);

namespace App\Domain\ConventionalCommit;

use App\Domain\DomainException\EmptyFile;
use App\Domain\DomainException\FileIsNotJson;
use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\Entity\Release;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;
use Ergebnis\Json\Printer\Printer;
use JsonException;

class FilesUpdater
{
    private array $files;
    private Release $release;
    private FilesToReleaseRepository $filesToReleaseRepository;

    public function __construct(array $files, Release $release, FilesToReleaseRepository $filesToReleaseRepository)
    {
        $this->files = $files;
        $this->release = $release;
        $this->filesToReleaseRepository = $filesToReleaseRepository;
    }

    public function makeRelease(): array
    {
        $files = [];

        /**
         * @var $fileToCopy File
         */
        foreach ($this->files as $fileToCopy) {
            $files[] = $fileToCopy;
        }

        $printer = new Printer();

        /**
         * @var $file File
         */
        foreach ($files as $file) {
            $content = $file->getContent();

            if (!$content) {
                throw new EmptyFile($file->getPath() . ' ' . $file->getName());
            }

            try {
                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $error) {
                throw new FileIsNotJson($file->getPath() . ' ' . $file->getName());
            }

            $content->version = $this->release->getVersion();

            $ident = '  ';

            if (strpos($file->getName(), 'package') === false) {
                 $ident = '    ';
            }

            $printed = $printer->print(
                json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK),
                $ident
            );

            $file->setContent($printed);
        }

        return $files;
    }
}