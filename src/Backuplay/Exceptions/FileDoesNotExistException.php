<?php
namespace Gummibeer\Backuplay\Exceptions;

use Exception;

class FileDoesNotExistException extends Exception
{

    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $this->message = $message . ' does not exist';
    }
}