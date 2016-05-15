<?php

namespace Gummibeer\Backuplay\Artisan;

use Gummibeer\Backuplay\Exceptions\FileDoesNotExistException;
use Gummibeer\Backuplay\Helpers\Archive;
use Gummibeer\Backuplay\Helpers\File;
use Gummibeer\Backuplay\Parsers\Filename;
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
            Storage::disk($disk)->put($filePath, file_get_contents($tempPath));
            if (! Storage::disk($disk)->exists($filePath)) {
                throw new FileDoesNotExistException($filePath);
            }
            $this->info($cycle.' archive stored');
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
        $scripts = $this->config->get('scripts.before', []);
        if(count($scripts) == 0) {
            $this->info('no scripts.before found');
            return true;
        }

        $success = true;
        foreach($scripts as $script) {
            $this->info('script.before run: '.$script);
            $success = $this->runScript($script) ? $success : false;
        }
        return $success;
    }

    /**
     * @return bool
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    protected function runAfterScripts()
    {
        $scripts = $this->config->get('scripts.after', []);
        if(count($scripts) == 0) {
            $this->info('no scripts.after found');
            return true;
        }

        $success = true;
        foreach($scripts as $script) {
            $this->info('script.after run: '.$script);
            $success = $this->runScript($script) ? $success : false;
        }
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
        if(!$process->isSuccessful()) {
            if($this->config->isStrict()) {
                throw new ProcessFailedException($process);
            }
            $this->error('script failed: '.$script);
            return false;
        }
        $this->comment($process->getOutput());
        return true;
    }
}
