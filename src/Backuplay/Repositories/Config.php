<?php

namespace Gummibeer\Backuplay\Repositories;

use Gummibeer\Backuplay\Contracts\ConfigContract;
use Gummibeer\Backuplay\Exceptions\EntityIsNoDirectoryException;
use Gummibeer\Backuplay\Helpers\File;
use Illuminate\Config\Repository;
use Illuminate\Support\Collection;

/**
 * Class ConfigRepo.
 */
class Config extends Repository implements ConfigContract
{
    /**
     * ConfigRepo constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $items = app('config')->get('backuplay');
        parent::__construct($items);
    }

    /**
     * @return array
     */
    public function getFolders()
    {
        return (new Collection($this->get('folders')))
            ->filter(function ($folder) {
                return File::isExisting($folder) === true ? true : false;
            })
            ->filter(function ($folder) {
                return File::isDir($folder) === true ? true : false;
            })
            ->filter(function ($folder) {
                return File::isReadable($folder) === true ? true : false;
            })
            ->sort('strnatcmp')
            ->toArray();
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return (new Collection($this->get('files')))
            ->filter(function ($file) {
                return File::isExisting($file) === true ? true : false;
            })
            ->filter(function ($file) {
                return File::isFile($file) === true ? true : false;
            })
            ->filter(function ($file) {
                return File::isReadable($file) === true ? true : false;
            })
            ->sort('strnatcmp')
            ->toArray();
    }

    /**
     * @param bool|null $strict
     * @return bool
     */
    public function isStrict($strict = null)
    {
        if (is_null($strict)) {
            $strict = $this->get('strict');
        }

        return (bool) $strict;
    }

    /**
     * @return string
     * @throws \Gummibeer\Backuplay\Exceptions\EntityIsNoDirectoryException
     * @throws \Gummibeer\Backuplay\Exceptions\FileIsntWritableException
     */
    public function getTempDir()
    {
        $dir = $this->get('temp_path.dir');
        $chmod = $this->get('temp_path.chmod');
        if (! (File::isDir($dir, false) === true ? true : false)) {
            try {
                $success = mkdir($dir, $chmod, true);
            } catch(\ErrorException $exception) {
                $success = false;
            }
            if (! $success) {
                throw new EntityIsNoDirectoryException($dir);
            }
        }

        File::isWritable($dir, true);

        return rtrim($dir, DIRECTORY_SEPARATOR);
    }
}
