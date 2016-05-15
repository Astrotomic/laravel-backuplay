<?php

namespace Gummibeer\Backuplay\Helpers;

use Gummibeer\Backuplay\Exceptions\FileDoesNotExistException;
use Gummibeer\Backuplay\Exceptions\FileIsntReadableException;
use Gummibeer\Backuplay\Exceptions\FileIsntWritableException;
use Gummibeer\Backuplay\Exceptions\EntityIsNoDirectoryException;
use Gummibeer\Backuplay\Exceptions\EntityIsNoFileException;
use Gummibeer\Backuplay\Exceptions\Handler as ExceptionHandler;

class File
{
    /**
     * @param string $file
     * @param bool|null $strict
     * @return bool|\Gummibeer\Backuplay\Exceptions\FileDoesNotExistException
     * @throws \Gummibeer\Backuplay\Exceptions\FileDoesNotExistException
     */
    public static function isExisting($file, $strict = null)
    {
        if (file_exists($file)) {
            return true;
        }
        return ExceptionHandler::handle(new FileDoesNotExistException($file), $strict);
    }

    /**
     * @param string $dir
     * @param bool|null $strict
     * @return bool|\Gummibeer\Backuplay\Exceptions\EntityIsNoDirectoryException
     * @throws \Gummibeer\Backuplay\Exceptions\EntityIsNoDirectoryException
     */
    public static function isDir($dir, $strict = null)
    {
        if (is_dir($dir)) {
            return true;
        }
        return ExceptionHandler::handle(new EntityIsNoDirectoryException($dir), $strict);
    }

    /**
     * @param string $file
     * @param bool|null $strict
     * @return bool|\Gummibeer\Backuplay\Exceptions\EntityIsNoFileException
     * @throws \Gummibeer\Backuplay\Exceptions\EntityIsNoFileException
     */
    public static function isFile($file, $strict = null)
    {
        if (is_file($file)) {
            return true;
        }
        return ExceptionHandler::handle(new EntityIsNoFileException($file), $strict);
    }

    /**
     * @param string $file
     * @param bool|null $strict
     * @return bool|\Gummibeer\Backuplay\Exceptions\FileIsntReadableException
     * @throws \Gummibeer\Backuplay\Exceptions\FileIsntReadableException
     */
    public static function isReadable($file, $strict = null)
    {
        if (is_readable($file)) {
            return true;
        }
        return ExceptionHandler::handle(new FileIsntReadableException($file), $strict);
    }

    /**
     * @param string $file
     * @param bool|null $strict
     * @return bool|\Gummibeer\Backuplay\Exceptions\FileIsntWritableException
     * @throws \Gummibeer\Backuplay\Exceptions\FileIsntWritableException
     */
    public static function isWritable($file, $strict = null)
    {
        if (is_writable($file)) {
            return true;
        }
        return ExceptionHandler::handle(new FileIsntWritableException($file), $strict);
    }
}
