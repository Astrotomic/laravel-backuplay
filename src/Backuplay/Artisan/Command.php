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
        $string = '[ERROR] '.$string;
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
        $string = '[WARN] '.$string;
        parent::warn($string, $verbosity);
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
        $string = '[INFO] '.$string;
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
        $string = '[DEBUG] '.$string;
        parent::comment($string, $verbosity);
    }
}
