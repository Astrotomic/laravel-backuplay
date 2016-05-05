<?php

namespace Gummibeer\Backuplay\Exceptions;

use Exception;

/**
 * Class IsNoDirectoryException
 * @package Gummibeer\Backuplay
 * @subpackage Gummibeer\Backuplay\Exceptions
 */
class IsNoDirectoryException extends Exception
{
    /**
     * IsNoDirectoryException constructor.
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $this->message = $message.' isn\'t a directory';
    }
}
