<?php

namespace Gummibeer\Backuplay\Exceptions;

use Exception;

/**
 * Class FileDoesNotExistException
 * @package Gummibeer\Backuplay
 * @subpackage Gummibeer\Backuplay\Exceptions
 */
class FileDoesNotExistException extends Exception
{
    /**
     * FileDoesNotExistException constructor.
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $this->message = $message.' does not exist';
    }
}
