<?php
declare(strict_types=1);

namespace App\Domain\DomainException;

use Exception;
use Throwable;

class FileIsNotJson extends Exception
{

    public function __construct(string $fileName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('File %s is not json', $fileName), $code, $previous);
    }
}