<?php

use Gummibeer\Backuplay\Artisan\CreateBackup;
use Gummibeer\Backuplay\Contracts\ConfigContract;

class CreateBackupCommandTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $storagePath = realpath(implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            '..',
            'storage',
            'testing',
        ]));

        $tempPath = realpath(implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            '..',
            'storage',
            'temp',
        ]));

        $globalConfig = app('config');
        $globalConfig->set('filesystems.disks.testing', [
            'driver' => 'local',
            'root' => $storagePath,
        ]);

        $config = app(ConfigContract::class);
        $config->set('disk', 'testing');
        $config->set('temp_path.dir', $tempPath);
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
        $this->runCommand($command);
    }

    protected function runCommand($command, $input = [])
    {
        return $command->run(new \Symfony\Component\Console\Input\ArrayInput($input), new \Symfony\Component\Console\Output\NullOutput);
    }
}
