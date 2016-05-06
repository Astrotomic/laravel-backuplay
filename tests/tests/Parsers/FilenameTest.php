<?php

use Gummibeer\Backuplay\Parsers\Filename;
use Gummibeer\Backuplay\Contracts\ConfigContract;

class FilenameTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $config = app(ConfigContract::class);

        $config->set('folders', [__DIR__]);
        $config->set('files', [__FILE__]);
        $config->set('extension', 'zip');
        $config->set('storage_filename', '{hash}.{date:N}');
    }

    /** @test */
    public function parseShouldReturnString()
    {
        $filename = new Filename();
        $parsed = $filename->parse();
        $hash = md5(__DIR__.' '.__FILE__);
        $date = date('N');
        $this->assertEquals($hash.'.'.$date.'.zip', $parsed);
    }

    /** @test */
    public function toStringShouldReturnParsedString()
    {
        $filename = new Filename();
        $hash = md5(__DIR__.' '.__FILE__);
        $date = date('N');
        $this->assertEquals($hash.'.'.$date.'.zip', (string) $filename);
    }
}