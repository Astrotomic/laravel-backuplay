<?php

namespace Gummibeer\Backuplay\Exceptions;

use Exception;

/**
 * Class FileIsntReadableException
 * @package Gummibeer\Backuplay
 * @subpackage Gummibeer\Backuplay\Exceptions
 */
class FileIsntReadableException extends Exception
{
    /**
     * FileIsntReadableException constructor.
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $this->message = $message.' isn\'t readable';
    }
}
