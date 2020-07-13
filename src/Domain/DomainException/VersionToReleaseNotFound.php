<?php
declare(strict_types=1);

namespace App\Domain\DomainException;

use Exception;
use Throwable;

class VersionToReleaseNotFound extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('Could not find conventional commit version in the commit list', $code, $previous);
    }
}