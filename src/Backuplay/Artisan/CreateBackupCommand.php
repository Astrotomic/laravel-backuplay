<?php

namespace Gummibeer\Backuplay\Artisan;

use Illuminate\Support\Collection;

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
     * @var Collection
     */
    protected $folders;
    /**
     * @var Collection
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

    public function fire()
    {
        $this->info('start backuplay');

        $this->folders = $this->getFolders();
        $this->comment('backup folders: '.$this->folders->implode(' '));
        $this->files = $this->getFiles();
        $this->comment('backup files: '.$this->files->implode(' '));

        if ($this->folders->count() > 0 || $this->files->count() > 0) {
        } else {
            $this->error('no valid folders or files to backup');
        }

        $this->info('end backuplay');
    }

    /**
     * @return Collection
     */
    protected function getFolders()
    {
        return collect($this->config['folders'])
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
     * @return Collection
     */
    protected function getFiles()
    {
        return collect($this->config['files'])
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
}
