<?php

namespace Gummibeer\Backuplay\Artisan;

use Gummibeer\Backuplay\Exceptions\FileDoesNotExistException;
use Gummibeer\Backuplay\Exceptions\FileIsntReadableException;
use Gummibeer\Backuplay\Exceptions\FileIsntWritableException;
use Gummibeer\Backuplay\Exceptions\IsNoDirectoryException;
use Gummibeer\Backuplay\Exceptions\IsNoFileException;
use Illuminate\Console\Command as IlluminateCommand;

class Command extends IlluminateCommand
{
    public function error($string, $verbosity = null)
    {
        $string = '[ERROR] '.$string;
        parent::error($string, $verbosity);
    }

    public function info($string, $verbosity = null)
    {
        $string = '[INFO] '.$string;
        parent::info($string, $verbosity);
    }

    public function comment($string, $verbosity = null)
    {
        $string = '[DEBUG] '.$string;
        parent::comment($string, $verbosity);
    }

    protected function isStrict($strict = null)
    {
        if (is_null($strict)) {
            $strict = config('backuplay.strict');
        }

        return (bool) $strict;
    }

    protected function isExisting($file, $strict = null)
    {
        if (file_exists($file)) {
            return true;
        } else {
            self::handleException(new FileDoesNotExistException($file), $strict);

            return false;
        }
    }

    protected function isDir($dir, $strict = null)
    {
        if (is_dir($dir)) {
            return true;
        } else {
            self::handleException(new IsNoDirectoryException($dir), $strict);

            return false;
        }
    }

    protected function isFile($file, $strict = null)
    {
        if (is_file($file)) {
            return true;
        } else {
            self::handleException(new IsNoFileException($file), $strict);

            return false;
        }
    }

    protected function isReadable($file, $strict = null)
    {
        if (is_readable($file)) {
            return true;
        } else {
            self::handleException(new FileIsntReadableException($file), $strict);

            return false;
        }
    }

    protected function isWritable($file, $strict = null)
    {
        if (is_writable($file)) {
            return true;
        } else {
            self::handleException(new FileIsntWritableException($file), $strict);

            return false;
        }
    }

    protected function handleException(\Exception $e, $strict)
    {
        if (self::isStrict($strict)) {
            throw $e;
        } else {
            $this->error($e->getMessage());
        }
    }
}
