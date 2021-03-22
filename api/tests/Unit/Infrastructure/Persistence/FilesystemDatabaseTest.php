<?php
declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Persistence;

use App\Domain\Gitlab\Entity\File;
use App\Infrastructure\Persistence\FilesystemDatabase;
use Filebase\Database;
use Filebase\Document;
use Filebase\Query;
use PHPUnit\Framework\TestCase;
use Exception;

class FilesystemDatabaseTest extends TestCase
{

    private Database $database;
    private int $projectId;

    public function setUp(): void
    {
        parent::setUp();
        $this->database = $this->createMock(Database::class);
        $this->projectId = 1;
    }

    public function test_save_document()
    {
        $file = new File('1', 'file.txt', '', '');
        $this->database->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $filesystem = new FilesystemDatabase($this->database);
        $filesystem->save($this->projectId, $file);
    }

    public function test_error_while_saving_document()
    {
        $this->expectException(Exception::class);

        $file = new File('1', 'file.txt', '', '');
        $this->database->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $filesystem = new FilesystemDatabase($this->database);
        $filesystem->save($this->projectId, $file);
    }

    public function test_delete_document()
    {
        $file = new File('1', 'file.txt', '', '');
        $this->database->expects($this->once())
            ->method('get')
            ->willReturn(new Document($this->database));

        $this->database->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $filesystem = new FilesystemDatabase($this->database);
        $filesystem->delete($this->projectId, $file);
    }

    public function test_error_while_deleting_document()
    {
        $this->expectException(Exception::class);

        $file = new File('1', 'file.txt', '', '');
        $this->database->expects($this->once())
            ->method('get')
            ->willReturn(new Document($this->database));

        $this->database->expects($this->once())
            ->method('delete')
            ->willReturn(false);

        $filesystem = new FilesystemDatabase($this->database);
        $filesystem->delete($this->projectId, $file);
    }

    public function test_retrieve_all_documents_based_on_project_id()
    {
        $document = $this->createMock(Document::class);
        $document->expects($this->once())
            ->method('getData')
            ->willReturn([
                'projectId' => 1,
                'path' => '/src',
                'name' => 'myfile.txt',
            ]);
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
            ->method('resultDocuments')
            ->willReturn([ $document ]);
        $query->expects($this->once())
            ->method('where')
            ->willReturnSelf();

        $this->database->expects($this->once())
            ->method('query')
            ->willReturn($query);

        $filesystem = new FilesystemDatabase($this->database);
        $filesystem->findAll($this->projectId);

    }
}
