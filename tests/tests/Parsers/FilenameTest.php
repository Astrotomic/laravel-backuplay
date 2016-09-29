<?php

use Astrotomic\Backuplay\Parsers\Filename;
use Astrotomic\Backuplay\Contracts\ConfigContract;

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
        $this->assertEquals('custom'.DIRECTORY_SEPARATOR.$hash.'.'.$date.'.zip', (string) $filename);
    }

    /** @test */
    public function cycleParseShouldReturnString()
    {
        $cycles = [
            'dailyW' => 'w',
            'dailyM' => 'j',
            'dailyY' => 'z',
            'weekly' => 'W',
            'monthly' => 'n',
            'custom' => 'N',
        ];

        foreach ($cycles as $cycle => $date) {
            $filename = (new Filename())->cycleParse($cycle);
            $hash = md5(__DIR__.' '.__FILE__);
            $date = date($date);
            $this->assertEquals($cycle.DIRECTORY_SEPARATOR.$hash.'.'.$date.'.zip', (string) $filename);
        }
    }
}
