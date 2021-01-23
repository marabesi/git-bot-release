<?php

namespace App\Domain\DomainException;

use Exception;
use Throwable;

class NoFilesToRelease extends Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Could not find any file to release %s', $message), $code, $previous);
    }
}