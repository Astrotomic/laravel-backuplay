<?php

namespace Gummibeer\Backuplay\Artisan;

use Gummibeer\Backuplay\Contracts\ConfigContract;
use Illuminate\Console\Command as IlluminateCommand;
use Illuminate\Support\Facades\Log;

/**
 * Class Command.
 */
class Command extends IlluminateCommand
{
    /**
     * @var \Gummibeer\Backuplay\Contracts\ConfigContract
     */
    protected $config;
    /**
     * @var array
     */
    protected $log = [
        'error' => [],
        'warning' => [],
        'info' => [],
        'debug' => [],
    ];

    /**
     * CreateBackup constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->config = app(ConfigContract::class);
    }

    /**
     * Write a string as error output.
     *
     * @param  string  $string
     * @param  null|int|string  $verbosity
     * @return void
     */
    public function error($string, $verbosity = null)
    {
        $string = $this->label('error').$string;
        $this->log('error', $string);
        parent::error($string, $verbosity);
    }

    /**
     * Write a string as warning output.
     *
     * @param  string  $string
     * @param  null|int|string  $verbosity
     * @return void
     */
    public function warn($string, $verbosity = null)
    {
        $string = $this->label('warn').$string;
        $this->log('warning', $string);
        if (is_callable('parent::warn')) {
            parent::warn($string, $verbosity);
        } else {
            parent::info($string, $verbosity);
        }
    }

    /**
     * Write a string as info output.
     *
     * @param  string  $string
     * @param  null|int|string  $verbosity
     * @return void
     */
    public function info($string, $verbosity = null)
    {
        $string = $this->label('info').$string;
        $this->log('info', $string);
        parent::info($string, $verbosity);
    }

    /**
     * Write a string as debug output.
     *
     * @param  string  $string
     * @param  null|int|string  $verbosity
     * @return void
     */
    public function comment($string, $verbosity = null)
    {
        $string = $this->label('debug').$string;
        $this->log('debug', $string);
        parent::comment($string, $verbosity);
    }

    /**
     * @param string $filepath
     */
    public function unlink($filepath)
    {
        if (file_exists($filepath)) {
            try {
                unlink($filepath);
            } catch (\ErrorException $exception) {
                $this->warn('file isn\'t deletable');
            }
        }
    }

    /**
     * @param string $level
     * @return string
     */
    public function label($level)
    {
        return '['.strtoupper($level).']['.$this->now().'] ';
    }

    /**
     * @return string
     */
    public function now()
    {
        return (string) date('Y-m-d H:i:s.u');
    }

    /**
     * @return void
     */
    public function log($level, $string)
    {
        $this->log[$level][] = $string;
        if($this->config->get('log')) {
            if(method_exists(Log::class, $level)) {
                call_user_func([Log::class, $level], [$string]);
            }
        }
    }
}
