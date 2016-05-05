<?php

namespace Gummibeer\Backuplay\Artisan;

use Alchemy\Zippy\Zippy;
use Gummibeer\Backuplay\Exceptions\EntityIsNoDirectoryException;
use Gummibeer\Backuplay\Exceptions\FileDoesNotExistException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Class CreateBackup.
 */
class CreateBackup extends Command
{
    /**
     * @var string
     */
    protected $name = 'backup:create';
    /**
     * @var string
     */
    protected $description = 'Create and store a new backup';

    /**
     * @var array
     */
    protected $config;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $folders;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $files;

    /**
     * CreateBackup constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->config = config('backuplay');
    }

    /**
     * @return void
     * @throws \Gummibeer\Backuplay\Exceptions\EntityIsNoDirectoryException
     * @throws \Gummibeer\Backuplay\Exceptions\FileDoesNotExistException
     * @throws \Gummibeer\Backuplay\Exceptions\EntityIsNoFileException
     */
    public function fire()
    {
        $this->info('start backuplay');

        $this->folders = $this->getFolders();
        $this->comment('backup folders: '.$this->folders->implode(' '));
        $this->files = $this->getFiles();
        $this->comment('backup files: '.$this->files->implode(' '));

        if ($this->folders->count() > 0 || $this->files->count() > 0) {
            $tempDir = $this->getTempDir();
            $tempName = md5(uniqid(date('U'))).'.'.$this->config['extension'];
            $tempPath = $tempDir.DIRECTORY_SEPARATOR.$tempName;
            $zippy = Zippy::load();
            $archive = $zippy->create($tempPath);

            if ($this->folders->count() > 0) {
                $this->comment('add folders to archive');
                foreach ($this->folders as $folder) {
                    $archive->addMembers($folder, true);
                }
            }

            if ($this->files->count() > 0) {
                $this->comment('add files to archive');
                foreach ($this->files as $file) {
                    $archive->addMembers($file, false);
                }
            }

            $this->isExisting($tempPath, true);
            $this->isFile($tempPath, true);
            $this->info('created archive');
        } else {
            $this->warn('no valid folders or files to backup');
        }

        $this->info('end backuplay');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function getFolders()
    {
        return (new Collection($this->config['folders']))
            ->filter(function ($folder) {
                return $this->isExisting($folder);
            })
            ->filter(function ($folder) {
                return $this->isDir($folder);
            })
            ->filter(function ($folder) {
                return $this->isReadable($folder);
            })
            ->sort();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function getFiles()
    {
        return (new Collection($this->config['files']))
            ->filter(function ($file) {
                return $this->isExisting($file);
            })
            ->filter(function ($file) {
                return $this->isFile($file);
            })
            ->filter(function ($file) {
                return $this->isReadable($file);
            })
            ->sort();
    }

    /**
     * @return string
     * @throws \Gummibeer\Backuplay\Exceptions\EntityIsNoDirectoryException
     */
    protected function getTempDir()
    {
        $dir = $this->config['temp_path']['dir'];
        $chmod = $this->config['temp_path']['chmod'];
        if (! $this->isDir($dir, false)) {
            $success = mkdir($dir, $chmod);
            if ($success) {
                $this->info('temporary directory created');
            } else {
                throw new EntityIsNoDirectoryException($dir);
            }
        }

        return rtrim($dir, DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $tempPath
     * @return void
     * @throws \Gummibeer\Backuplay\Exceptions\FileDoesNotExistException
     */
    protected function storeArchive($tempPath)
    {
        $disk = $this->config['disk'];
        if ($disk !== false) {
            $this->comment('store archive on disk: '.$disk);
            $filename = $this->getStorageFileName();
            $filePath = implode(DIRECTORY_SEPARATOR, array_filter([
                $this->config['storage_path'],
                $filename,
            ]));
            Storage::put($filePath, file_get_contents($tempPath));
            if (Storage::has($filePath)) {
                $this->info('archive stored');
            } else {
                throw new FileDoesNotExistException($filePath);
            }
        } else {
            $this->warn('storage is disabled');
        }
        unlink($tempPath);
    }

    /**
     * @return string
     */
    protected function getStorageFileName()
    {
        $filename = $this->config['storage_filename'];
        $unique = uniqid();
        $hash = md5($this->folders->implode(' ').' '.$this->files->implode(' '));

        $filename = str_replace('{unique}', $unique, $filename);
        $filename = str_replace('{hash}', $hash, $filename);
        $filename = preg_replace_callback('/\{date:([^\}]*)\}/', function ($hit) {
            return date($hit[1]);
        }, $filename);
        $filename .= '.'.$this->config['extension'];

        return strtolower($filename);
    }
}
