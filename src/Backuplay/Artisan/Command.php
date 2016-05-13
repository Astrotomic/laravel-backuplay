<?php

namespace Gummibeer\Backuplay\Artisan;

use Illuminate\Console\Command as IlluminateCommand;

/**
 * Class Command.
 */
class Command extends IlluminateCommand
{
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
        $string = $this->label('comment').$string;
        parent::comment($string, $verbosity);
    }

    public function unlink($filepath)
    {
        if (file_exists($filepath)) {
            try {
                unlink($filepath);
            } catch (\ErrorException $e) {
                $this->warn('file isn\'t deletable');
            }
        }
    }

    public function label($level)
    {
        return '['.strtoupper($level).']['.$this->now().'] ';
    }

    public function now()
    {
        return date('Y-m-d H:i:s.u');
    }
}
