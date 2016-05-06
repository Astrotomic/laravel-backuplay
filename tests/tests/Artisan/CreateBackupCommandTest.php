<?php

use Gummibeer\Backuplay\Parsers\Filename;
use Gummibeer\Backuplay\Artisan\CreateBackup;
use Gummibeer\Backuplay\Contracts\ConfigContract;
use Symfony\Component\Console\Output\BufferedOutput;

class CreateBackupCommandTest extends TestCase
{
    protected $storagePath;
    protected $tempPath;

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

        $config = app(ConfigContract::class);
        $config->set('disk', 'testing');
        $config->set('temp_path.dir', $this->tempPath);
        $config->set('folders', [__DIR__]);
        $config->set('files', [__FILE__]);
        $config->set('extension', 'zip');
        $config->set('storage_filename', '{hash}.{date:N}');
    }

    /** @test */
    public function createBackup()
    {
        $command = new CreateBackup();
        $command->setLaravel($this->app);
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $this->assertTrue(file_exists($this->storagePath.DIRECTORY_SEPARATOR.(new Filename())));
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('[INFO] end backuplay', $output);
    }
}
