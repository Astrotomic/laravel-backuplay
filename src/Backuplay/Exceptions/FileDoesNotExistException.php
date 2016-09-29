<?php

namespace Astrotomic\Backuplay\Exceptions;

use Exception;

/**
 * Class FileDoesNotExistException.
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
        parent::__construct($message, $code, $previous);
    }
}
