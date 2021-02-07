<?php

namespace App\Repository;

use Exception;
use Throwable;

class NotFoundException extends Exception implements Throwable
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
