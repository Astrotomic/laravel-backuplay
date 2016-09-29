<?php

use Astrotomic\Backuplay\Parsers\Filename;
use Astrotomic\Backuplay\Artisan\CreateBackup;
use Astrotomic\Backuplay\Artisan\ListBackup;
use Astrotomic\Backuplay\Contracts\ConfigContract;
use Symfony\Component\Console\Output\BufferedOutput;

class ListBackupCommandTest extends TestCase
{
    protected $storagePath;
    protected $tempPath;
    protected $config;

    public function setUp()
    {
        parent::setUp();

        $this->storagePath = realpath(implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            '..',
            'storage',
            'testing',
        ]));

        $this->tempPath = realpath(implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            '..',
            'storage',
            'temp',
        ]));

        $globalConfig = app('config');
        $globalConfig->set('filesystems.disks.testing', [
            'driver' => 'local',
            'root' => $this->storagePath,
        ]);

        $this->config = app(ConfigContract::class);
        $this->config->set('disk', 'testing');
        $this->config->set('temp_path.dir', $this->tempPath);
        $this->config->set('folders', [__DIR__]);
        $this->config->set('files', [__FILE__]);
        $this->config->set('extension', 'ziphp');
        $this->config->set('storage_cycle', ['custom']);
        $this->config->set('storage_filename', '{hash}.{date:N}');
    }

    /** @test */
    public function listBackupWithoutStorage()
    {
        $this->config->set('disk', false);
        $command = new ListBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('storage is disabled', $output);
        $this->assertContains('end backuplay', $output);
    }

    /** @test */
    public function listBackupWithStorage()
    {
        $this->config->set('disk', 'testing');
        $command = new CreateBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);

        $filename = new Filename();
        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.$filename;
        $this->assertTrue(file_exists($storageFile));

        $command = new ListBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('list archives on disk: testing', $output);
        $this->assertContains(basename($filename), $output);
        $this->assertContains('end backuplay', $output);

        $this->unlink($storageFile);
    }

    /** @test */
    public function listBackupWithEmptyStorage()
    {
        $command = new ListBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('list archives on disk: testing', $output);
        $this->assertContains('no backups found', $output);
        $this->assertContains('end backuplay', $output);
    }

    /** @test */
    public function listBackupWithStorageForCycle()
    {
        $this->config->set('disk', 'testing');
        $command = new CreateBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);

        $filename = new Filename();
        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.$filename;
        $this->assertTrue(file_exists($storageFile));

        $command = new ListBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, ['--cycle' => 'custom'], $output);
        $output = $output->fetch();
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('list archives on disk: testing', $output);
        $this->assertContains(basename($filename), $output);
        $this->assertContains('end backuplay', $output);

        $this->unlink($storageFile);
    }

    /** @test */
    public function listBackupWithStorageForEmptyCycle()
    {
        $this->config->set('disk', 'testing');
        $command = new CreateBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);

        $filename = new Filename();
        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.$filename;
        $this->assertTrue(file_exists($storageFile));

        $command = new ListBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, ['--cycle' => 'dailyW'], $output);
        $output = $output->fetch();
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('list archives on disk: testing', $output);
        $this->assertContains('no backups found', $output);
        $this->assertContains('end backuplay', $output);

        $this->unlink($storageFile);
    }
}
