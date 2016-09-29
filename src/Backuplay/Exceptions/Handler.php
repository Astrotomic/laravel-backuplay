<?php

namespace Astrotomic\Backuplay\Exceptions;

use Astrotomic\Backuplay\Contracts\ConfigContract;

class Handler
{
    /**
     * @param \Exception $exception
     * @param bool|null $strict
     * @throws \Exception
     * @return \Exception
     */
    public static function handle(\Exception $exception, $strict = null)
    {
        if (app(ConfigContract::class)->isStrict($strict)) {
            throw $exception;
        }

        return $exception;
    }
}
