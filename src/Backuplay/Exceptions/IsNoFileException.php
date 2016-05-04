<?php

namespace Gummibeer\Backuplay\Exceptions;

use Exception;

class IsNoFileException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $this->message = $message.' isn\'t a file';
    }
}