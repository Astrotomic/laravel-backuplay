<?php

use Astrotomic\Backuplay\Parsers\Filename;
use Astrotomic\Backuplay\Artisan\CreateBackup;
use Astrotomic\Backuplay\Contracts\ConfigContract;
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
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.(new Filename());
        $this->assertFalse(file_exists($storageFile));
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('storage is disabled', $output);
        $this->assertContains('end backuplay', $output);
        $this->unlink($storageFile);
    }

    /** @test */
    public function createBackupWithStorage()
    {
        $this->config->set('disk', 'testing');
        $command = new CreateBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.(new Filename());
        $this->assertTrue(file_exists($storageFile));
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('end backuplay', $output);
        $this->unlink($storageFile);
    }

    /** @test */
    public function createBackupWithoutEntries()
    {
        $this->config->set('folders', []);
        $this->config->set('files', []);
        $command = new CreateBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.(new Filename());
        $this->assertFalse(file_exists($storageFile));
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('no valid folders or files to backup', $output);
        $this->assertContains('end backuplay', $output);
        $this->unlink($storageFile);
    }

    /** @test */
    public function createBackupWithBeforeScripts()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $scripts = ['dir'];
        } else {
            $scripts = ['ls -la'];
        }

        $this->config->set('disk', 'testing');
        $this->config->set('scripts.before', $scripts);
        $command = new CreateBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.(new Filename());
        $this->assertTrue(file_exists($storageFile));
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('script.before run', $output);
        $this->assertContains('composer.json', $output);
        $this->assertContains('end backuplay', $output);
        $this->unlink($storageFile);
    }

    /** @test */
    public function createBackupWithEmptyBeforeScripts()
    {
        $this->config->set('disk', 'testing');
        $this->config->set('scripts.before', []);
        $command = new CreateBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.(new Filename());
        $this->assertTrue(file_exists($storageFile));
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('no scripts.before found', $output);
        $this->assertContains('end backuplay', $output);
        $this->unlink($storageFile);
    }

    /** @test */
    public function createBackupWithFailingBeforeScripts()
    {
        $this->config->set('disk', 'testing');
        $this->config->set('scripts.before', ['foobar --foo --bar']);
        $command = new CreateBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.(new Filename());
        $this->assertTrue(file_exists($storageFile));
        $this->assertContains('[ERROR]', $output);
        $this->assertContains('script.before run', $output);
        $this->assertContains('script failed', $output);
        $this->assertContains('end backuplay', $output);
        $this->unlink($storageFile);
    }

    /** @test */
    public function createBackupWithStrictFailingBeforeScripts()
    {
        $this->config->set('disk', 'testing');
        $this->config->set('strict', true);
        $this->config->set('scripts.before', ['foobar --foo --bar']);
        $this->setExpectedException(\Symfony\Component\Process\Exception\ProcessFailedException::class);

        $command = new CreateBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.(new Filename());
        $this->assertFalse(file_exists($storageFile));
        $this->unlink($storageFile);
    }

    /** @test */
    public function createBackupWithAfterScripts()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $scripts = ['dir'];
        } else {
            $scripts = ['ls -la'];
        }

        $this->config->set('disk', 'testing');
        $this->config->set('scripts.after', $scripts);
        $command = new CreateBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.(new Filename());
        $this->assertTrue(file_exists($storageFile));
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('script.after run', $output);
        $this->assertContains('composer.json', $output);
        $this->assertContains('end backuplay', $output);
        $this->unlink($storageFile);
    }

    /** @test */
    public function createBackupWithEmptyAfterScripts()
    {
        $this->config->set('disk', 'testing');
        $this->config->set('scripts.after', []);
        $command = new CreateBackup();
        $output = new BufferedOutput();
        $this->runCommand($command, [], $output);
        $output = $output->fetch();

        $storageFile = $this->storagePath.DIRECTORY_SEPARATOR.(new Filename());
        $this->assertTrue(file_exists($storageFile));
        $this->assertNotContains('[ERROR]', $output);
        $this->assertContains('no scripts.after found', $output);
        $this->assertContains('end backuplay', $output);
        $this->unlink($storageFile);
    }
}
