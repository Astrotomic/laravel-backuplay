<?php

use Gummibeer\Backuplay\Parsers\Filename;
use Gummibeer\Backuplay\Artisan\CreateBackup;
use Gummibeer\Backuplay\Contracts\ConfigContract;
use Symfony\Component\Console\Output\BufferedOutput;

class CreateBackupCommandTest extends TestCase
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
    public function createBackupWithoutStorage()
    {
        $this->config->set('disk', false);
        $command = new CreateBackup();
        $command->setLaravel($this->app);
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.(new Filename());
        $this->assertFalse(file_exists($storageFile));
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('[WARN] storage is disabled', $output);
        $this->assertContains('[INFO] end backuplay', $output);
        $this->unlink($storageFile);
    }

    /** @test */
    public function createBackupWithStorage()
    {
        $this->config->set('disk', 'testing');
        $command = new CreateBackup();
        $command->setLaravel($this->app);
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.(new Filename());
        $this->assertTrue(file_exists($storageFile));
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('[INFO] end backuplay', $output);
        $this->unlink($storageFile);
    }

    /** @test */
    public function createBackupWithoutEntries()
    {
        $this->config->set('folders', []);
        $this->config->set('files', []);
        $command = new CreateBackup();
        $command->setLaravel($this->app);
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.(new Filename());
        $this->assertFalse(file_exists($storageFile));
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('[WARN] no valid folders or files to backup', $output);
        $this->assertContains('[INFO] end backuplay', $output);
        $this->unlink($storageFile);
    }

    protected function unlink($filepath)
    {
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}
