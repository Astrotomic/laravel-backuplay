<?php
namespace Gummibeer\Backuplay\Exceptions;

use Gummibeer\Backuplay\Contracts\ConfigContract;

class Handler
{
    /**
     * @param \Exception $e
     * @param bool|null $strict
     * @throws \Exception
     * @return \Exception
     */
    public static function handle(\Exception $e, $strict = null)
    {
        if (app(ConfigContract::class)->getStrict($strict)) {
            throw $e;
        } else {
            return $e;
        }
    }
}