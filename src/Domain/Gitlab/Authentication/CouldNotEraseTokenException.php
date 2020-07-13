<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Authentication;

use Exception;
use Throwable;

class CouldNotEraseTokenException extends Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('could not erase token', $code, $previous);
    }
}