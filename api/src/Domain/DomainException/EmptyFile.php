<?php
declare(strict_types=1);

namespace App\Domain\DomainException;

use Exception;
use Throwable;

class EmptyFile extends Exception
{

    public function __construct($file = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('File ' . $file . ' is empty', $code, $previous);
    }
}