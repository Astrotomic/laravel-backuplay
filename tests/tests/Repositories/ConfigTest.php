<?php

use Gummibeer\Backuplay\Contracts\ConfigContract;

class ConfigTest extends TestCase
{
    /** @test */
    public function getStrictShouldReturnFalse()
    {
        $config = app(ConfigContract::class);
        $config->set('strict', false);
        $this->assertFalse($config->get('strict'));
    }

    /** @test */
    public function getStrictShouldReturnTrue()
    {
        $config = app(ConfigContract::class);
        $config->set('strict', true);
        $this->assertTrue($config->get('strict'));
    }

    /** @test */
    public function getRawFoldersShouldReturnAnArray()
    {
        $config = app(ConfigContract::class);
        $config->set('folders', [
            0 => '/test/folder',
        ]);
        $this->assertArrayHasKey(0, $config->get('folders'));
    }

    /** @test */
    public function getFilteredFoldersShouldReturnAnEmptyArray()
    {
        $config = app(ConfigContract::class);
        $config->set('folders', [
            0 => '/test/folder/that/wont/exist/on/any/system',
        ]);
        $this->assertCount(0, $config->getFolders());
    }

    /** @test */
    public function getFilteredFoldersShouldReturnAnArray()
    {
        $config = app(ConfigContract::class);
        $config->set('folders', [
            0 => '/test/folder/that/wont/exist/on/any/system',
            1 => __DIR__,
            2 => __FILE__,
        ]);
        $this->assertCount(1, $config->getFolders());
        $this->assertArrayHasKey(1, $config->getFolders());
    }

    /** @test */
    public function getRawFilesShouldReturnAnArray()
    {
        $config = app(ConfigContract::class);
        $config->set('files', [
            0 => '/test/folder/that/wont/exist/on/any/system/file.txt',
        ]);
        $this->assertArrayHasKey(0, $config->get('files'));
    }

    /** @test */
    public function getFilteredFilesShouldReturnAnEmptyArray()
    {
        $config = app(ConfigContract::class);
        $config->set('folders', [
            0 => '/test/folder/that/wont/exist/on/any/system/file.txt',
        ]);
        $this->assertCount(0, $config->getFiles());
    }

    /** @test */
    public function getFilteredFilesShouldReturnAnArray()
    {
        $config = app(ConfigContract::class);
        $config->set('files', [
            0 => '/test/folder/that/wont/exist/on/any/system/file.txt',
            1 => __DIR__,
            2 => __FILE__,
        ]);
        $this->assertCount(1, $config->getFiles());
        $this->assertArrayHasKey(2, $config->getFiles());
    }

    /** @test */
    public function getTempDirShouldReturnPath()
    {
        $config = app(ConfigContract::class);
        $config->set('temp_path.dir', realpath(implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            '..',
            'storage',
            'temp',
        ])));
        $this->assertStringEndsWith('temp', $config->getTempDir());
        $this->assertTrue(file_exists($config->getTempDir()));
    }

    /** @test */
    public function getTempDirShouldCreateAndReturnPath()
    {
        $config = app(ConfigContract::class);
        $config->set('temp_path.dir', implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            '..',
            'storage',
            'temp',
            'not',
            'existing',
            'path',
        ]));
        $this->assertStringEndsWith('path', $config->getTempDir());
        $this->assertTrue(file_exists($config->getTempDir()));
        $this->unlink($config->getTempDir());
    }

    /** @test */
    public function getTempDirShouldFailWithExistingFile()
    {
        $config = app(ConfigContract::class);
        $config->set('temp_path.dir', __FILE__);
        $this->setExpectedException(\Gummibeer\Backuplay\Exceptions\EntityIsNoDirectoryException::class);
        $config->getTempDir();
    }
}
