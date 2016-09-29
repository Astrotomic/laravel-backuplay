<?php

namespace Astrotomic\Backuplay\Helpers;

use Astrotomic\Backuplay\Exceptions\FileDoesNotExistException;
use Astrotomic\Backuplay\Exceptions\FileIsntReadableException;
use Astrotomic\Backuplay\Exceptions\FileIsntWritableException;
use Astrotomic\Backuplay\Exceptions\EntityIsNoDirectoryException;
use Astrotomic\Backuplay\Exceptions\EntityIsNoFileException;
use Astrotomic\Backuplay\Exceptions\Handler as ExceptionHandler;

class File
{
    /**
     * @param string $file
     * @param bool|null $strict
     * @return bool|\Astrotomic\Backuplay\Exceptions\FileDoesNotExistException
     * @throws \Astrotomic\Backuplay\Exceptions\FileDoesNotExistException
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
     * @return bool|\Astrotomic\Backuplay\Exceptions\EntityIsNoDirectoryException
     * @throws \Astrotomic\Backuplay\Exceptions\EntityIsNoDirectoryException
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
     * @return bool|\Astrotomic\Backuplay\Exceptions\EntityIsNoFileException
     * @throws \Astrotomic\Backuplay\Exceptions\EntityIsNoFileException
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
     * @return bool|\Astrotomic\Backuplay\Exceptions\FileIsntReadableException
     * @throws \Astrotomic\Backuplay\Exceptions\FileIsntReadableException
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
     * @return bool|\Astrotomic\Backuplay\Exceptions\FileIsntWritableException
     * @throws \Astrotomic\Backuplay\Exceptions\FileIsntWritableException
     */
    public static function isWritable($file, $strict = null)
    {
        if (is_writable($file)) {
            return true;
        }

        return ExceptionHandler::handle(new FileIsntWritableException($file), $strict);
    }
}
