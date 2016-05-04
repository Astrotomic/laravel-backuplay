<?php

namespace Gummibeer\Backuplay\Artisan;

class CreateBackup extends Command
{
    protected $name = 'backup:create';
    protected $description = 'Create and store a new backup';

    protected $config;
    protected $folders;
    protected $files;

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
