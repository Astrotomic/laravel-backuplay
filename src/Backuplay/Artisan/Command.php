<?php

namespace Gummibeer\Backuplay\Artisan;

use Gummibeer\Backuplay\Exceptions\FileDoesNotExistException;
use Gummibeer\Backuplay\Exceptions\FileIsntReadableException;
use Gummibeer\Backuplay\Exceptions\FileIsntWritableException;
use Gummibeer\Backuplay\Exceptions\EntityIsNoDirectoryException;
use Gummibeer\Backuplay\Exceptions\EntityIsNoFileException;
use Illuminate\Console\Command as IlluminateCommand;

/**
 * Class Command.
 */
class Command extends IlluminateCommand
{
    /**
     * Write a string as error output.
     *
     * @param  string  $string
     * @param  null|int|string  $verbosity
     * @return void
     */
    public function error($string, $verbosity = null)
    {
        $string = '[ERROR] '.$string;
        parent::error($string, $verbosity);
    }

    /**
     * Write a string as warning output.
     *
     * @param  string  $string
     * @param  null|int|string  $verbosity
     * @return void
     */
    public function warn($string, $verbosity = null)
    {
        $string = '[WARN] '.$string;
        parent::warn($string, $verbosity);
    }

    /**
     * Write a string as info output.
     *
     * @param  string  $string
     * @param  null|int|string  $verbosity
     * @return void
     */
    public function info($string, $verbosity = null)
    {
        $string = '[INFO] '.$string;
        parent::info($string, $verbosity);
    }

    /**
     * Write a string as debug output.
     *
     * @param  string  $string
     * @param  null|int|string  $verbosity
     * @return void
     */
    public function comment($string, $verbosity = null)
    {
        $string = '[DEBUG] '.$string;
        parent::comment($string, $verbosity);
    }

    /**
     * @param bool|null $strict
     * @return bool
     */
    protected function isStrict($strict = null)
    {
        if (is_null($strict)) {
            $strict = config('backuplay.strict');
        }

        return (bool) $strict;
    }

    /**
     * @param string $file
     * @param bool|null $strict
     * @return bool
     * @throws \Gummibeer\Backuplay\Exceptions\FileDoesNotExistException
     */
    protected function isExisting($file, $strict = null)
    {
        if (file_exists($file)) {
            return true;
        } else {
            self::handleException(new FileDoesNotExistException($file), $strict);

            return false;
        }
    }

    /**
     * @param string $dir
     * @param bool|null $strict
     * @return bool
     * @throws \Gummibeer\Backuplay\Exceptions\EntityIsNoDirectoryException
     */
    protected function isDir($dir, $strict = null)
    {
        if (is_dir($dir)) {
            return true;
        } else {
            self::handleException(new EntityIsNoDirectoryException($dir), $strict);

            return false;
        }
    }

    /**
     * @param string $file
     * @param bool|null $strict
     * @return bool
     * @throws \Gummibeer\Backuplay\Exceptions\EntityIsNoFileException
     */
    protected function isFile($file, $strict = null)
    {
        if (is_file($file)) {
            return true;
        } else {
            self::handleException(new EntityIsNoFileException($file), $strict);

            return false;
        }
    }

    /**
     * @param string $file
     * @param bool|null $strict
     * @return bool
     * @throws \Gummibeer\Backuplay\Exceptions\FileIsntReadableException
     */
    protected function isReadable($file, $strict = null)
    {
        if (is_readable($file)) {
            return true;
        } else {
            self::handleException(new FileIsntReadableException($file), $strict);

            return false;
        }
    }

    /**
     * @param string $file
     * @param bool|null $strict
     * @return bool
     * @throws \Gummibeer\Backuplay\Exceptions\FileIsntWritableException
     */
    protected function isWritable($file, $strict = null)
    {
        if (is_writable($file)) {
            return true;
        } else {
            self::handleException(new FileIsntWritableException($file), $strict);

            return false;
        }
    }

    /**
     * @param \Exception $e
     * @param bool|null $strict
     * @throws \Exception
     * @return void
     */
    protected function handleException(\Exception $e, $strict = null)
    {
        if (self::isStrict($strict)) {
            throw $e;
        } else {
            $this->error($e->getMessage());
        }
    }
}
