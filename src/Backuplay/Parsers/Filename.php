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

    /**
     * @param $cycle
     * @return string
     */
    public function cycleParse($cycle)
    {
        switch($cycle) {
            case "dailyW":
                $filename = $this->parse('{hash}.{date:w}');
                break;
            case "dailyM":
                $filename = $this->parse('{hash}.{date:j}');
                break;
            case "dailyY":
                $filename = $this->parse('{hash}.{date:z}');
                break;
            case "weekly":
                $filename = $this->parse('{hash}.{date:W}');
                break;
            case "monthly":
                $filename = $this->parse('{hash}.{date:n}');
                break;
            default:
                $filename = $this->parse();
                $cycle = 'custom';
                break;
        }
        return $cycle.DIRECTORY_SEPARATOR.$filename;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->cycleParse('custom');
    }
}
