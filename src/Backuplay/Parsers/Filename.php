<?php

namespace Gummibeer\Backuplay\Parsers;

use Gummibeer\Backuplay\Contracts\ConfigContract;
use Gummibeer\Backuplay\Contracts\ParserContract;

/**
 * Class FilenameParser.
 */
class Filename implements ParserContract
{
    /**
     * @var string|null
     */
    protected $string;

    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * FilenameParser constructor.
     */
    public function __construct()
    {
        $this->config = app(ConfigContract::class);
        $this->string = $this->config->get('storage_filename');
    }

    /**
     * @param string $string
     * @return string
     */
    public function parse($string = null)
    {
        if (is_null($string)) {
            $string = $this->config->get('storage_filename');
        }
        $filename = $string;
        $unique = uniqid();
        $hash = md5(implode(' ', $this->config->getFolders()).' '.implode(' ', $this->config->getFiles()));

        $filename = str_replace(['{unique}', '{hash}'], [$unique, $hash], $filename);
        $filename = preg_replace_callback('/\{date:([^\}]*)\}/', function ($hit) {
            return date($hit[1]);
        }, $filename);
        $filename .= '.'.$this->config->get('extension');

        return strtolower($filename);
    }

    public function __toString()
    {
        return $this->parse($this->string);
    }
}
