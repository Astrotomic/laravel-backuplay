<?php

namespace Gummibeer\Backuplay\Exceptions;

use Exception;

/**
 * Class FileIsntWritableException.
 */
class FileIsntWritableException extends Exception
{
    /**
     * FileIsntWritableException constructor.
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $this->message = $message.' isn\'t writable';
    }
}
