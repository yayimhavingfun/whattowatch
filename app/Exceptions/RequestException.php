<?php

namespace App\Exceptions;

use Exception;

class RequestException extends Exception
{
    public function getStatusCode()
    {
        return 400;
    }
}
