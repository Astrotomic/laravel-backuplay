<?php

namespace Gummibeer\Backuplay\Artisan;

use Gummibeer\Backuplay\Events\BackupCreateAfterCommand;
use Gummibeer\Backuplay\Events\BackupCreateAfterScripts;
use Gummibeer\Backuplay\Events\BackupCreateAfterStore;
use Gummibeer\Backuplay\Events\BackupCreateBeforeCommand;
use Gummibeer\Backuplay\Events\BackupCreateBeforeScripts;
use Gummibeer\Backuplay\Events\BackupCreateBeforeStore;
use Gummibeer\Backuplay\Events\BackupCreateFailedScript;
use Gummibeer\Backuplay\Events\BackupCreateFailedStore;
use Gummibeer\Backuplay\Exceptions\FileDoesNotExistException;
use Gummibeer\Backuplay\Helpers\Archive;
use Gummibeer\Backuplay\Helpers\File;
use Gummibeer\Backuplay\Parsers\Filename;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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
    protected $folders;
    /**
     * @var array
     */
    protected $files;

    /**
     * @return void
     * @throws \Gummibeer\Backuplay\Exceptions\EntityIsNoDirectoryException
     * @throws \Gummibeer\Backuplay\Exceptions\FileDoesNotExistException
     * @throws \Gummibeer\Backuplay\Exceptions\EntityIsNoFileException
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function fire()
    {
        $this->info('start backuplay');
        Event::fire(new BackupCreateBeforeCommand($this));

        $this->folders = $this->config->getFolders();
        $this->comment('backup folders: '.implode(' ', $this->folders));
        $this->files = $this->config->getFiles();
        $this->comment('backup files: '.implode(' ', $this->files));

        if ($this->isValidBackup()) {
            $this->runBeforeScripts();

            $tempDir = $this->config->getTempDir();
            $tempName = md5(uniqid(date('U'))).'.'.$this->config->get('extension');
            $tempPath = $tempDir.DIRECTORY_SEPARATOR.$tempName;
            $tempMeta = $this->createMetaFile($tempPath);
            $zippy = Archive::load();
            $archive = $zippy->create($tempPath, [
                'backup_info.txt' => $tempMeta,
            ]);
            $this->unlink($tempMeta);

            if (count($this->folders) > 0) {
                $this->comment('add folders to archive');
                foreach ($this->folders as $folder) {
                    $this->comment('add folder: '.$folder);
                    $archive->addMembers($folder, true);
                }
            }

            if (count($this->files) > 0) {
                $this->comment('add files to archive');
                foreach ($this->files as $file) {
                    $this->comment('add file: '.$file);
                    $archive->addMembers($file, false);
                }
            }

            File::isExisting($tempPath, true);
            File::isFile($tempPath, true);
            $this->info('created archive');
            $this->storeArchive($tempPath);

            $this->runAfterScripts();
        }

        Event::fire(new BackupCreateAfterCommand($this));
        $this->info('end backuplay');
    }

    /**
     * @param string $tempPath
     * @return string
     */
    protected function createMetaFile($tempPath)
    {
        $tempPath = str_replace($this->config->get('extension'), 'txt', $tempPath);
        file_put_contents($tempPath, $this->getMetaContent());

        return $tempPath;
    }

    /**
     * @return string
     */
    protected function getMetaContent()
    {
        $content = [];
        $content[] = date('Y-m-d H:i:s T');
        $content[] = 'Folders:';
        if (count($this->folders) > 0) {
            foreach ($this->folders as $folder) {
                $content[] = '* '.$folder;
            }
        }
        $content[] = 'Files:';
        if (count($this->files) > 0) {
            foreach ($this->files as $file) {
                $content[] = '* '.$file;
            }
        }

        return implode(PHP_EOL, $content);
    }

    /**
     * @param string $tempPath
     * @return bool
     * @throws \Gummibeer\Backuplay\Exceptions\FileDoesNotExistException
     */
    protected function storeArchive($tempPath)
    {
        $disk = $this->config->get('disk');
        if ($disk === false) {
            $this->warn('storage is disabled');

            return false;
        }
        $this->comment('store archive on disk: '.$disk);
        $filename = new Filename();
        foreach ($this->config->get('storage_cycle', []) as $cycle) {
            $this->comment('put '.$cycle.' archive in storage');
            $filePath = implode(DIRECTORY_SEPARATOR, array_filter([
                $this->config->get('storage_path'),
                $filename->cycleParse($cycle),
            ]));
            $content = file_get_contents($tempPath);
            Event::fire(new BackupCreateBeforeStore($this, $cycle, $filePath, $content));
            Storage::disk($disk)->put($filePath, $content);
            if (! Storage::disk($disk)->exists($filePath)) {
                Event::fire(new BackupCreateFailedStore($this, $cycle, $filePath, $content));
                throw new FileDoesNotExistException($filePath);
            }
            $this->info($cycle.' archive stored');
            Event::fire(new BackupCreateAfterStore($this, $cycle, $filePath, $content));
        }
        $this->unlink($tempPath);

        return true;
    }

    /**
     * @return bool
     */
    protected function hasFolders()
    {
        return (bool) (count($this->folders) > 0);
    }

    /**
     * @return bool
     */
    protected function hasFiles()
    {
        return (bool) (count($this->files) > 0);
    }

    /**
     * @return bool
     */
    protected function isValidBackup()
    {
        $valid = (bool) ($this->hasFolders() || $this->hasFiles());
        if (! $valid) {
            $this->warn('no valid folders or files to backup');
        }

        return $valid;
    }

    /**
     * @return bool
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    protected function runBeforeScripts()
    {
        return $this->runScripts('before');
    }

    /**
     * @return bool
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    protected function runAfterScripts()
    {
        return $this->runScripts('after');
    }

    /**
     * @param string $key
     * @return bool
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    protected function runScripts($key)
    {
        $scripts = $this->config->getScripts($key);
        Event::fire(new BackupCreateBeforeScripts($this, $key, $scripts));
        if (count($scripts) == 0) {
            $this->info("no scripts.{$key} found");

            return true;
        }

        $success = true;
        foreach ($scripts as $script) {
            $this->info("script.{$key} run: {$script}");
            $success = $this->runScript($script) ? $success : false;
        }

        Event::fire(new BackupCreateAfterScripts($this, $key, $scripts, $success));
        return $success;
    }

    /**
     * @param string $script
     * @return bool
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    protected function runScript($script)
    {
        $process = new Process($script);
        $process->run();
        if (! $process->isSuccessful()) {
        Event::fire(new BackupCreateFailedScript($this, $script, $process));
        if ($this->config->isStrict()) {
            throw new ProcessFailedException($process);
        }
        $this->error('script failed: '.$script);

        return false;
    }
        $this->comment($process->getOutput());

        return true;
    }
}
