<?php

use Gummibeer\Backuplay\Contracts\ConfigContract;

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
    public function disabled()
    {
        $this->assertTrue(true);
    }
}
