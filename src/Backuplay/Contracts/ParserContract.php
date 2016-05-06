<?php

namespace Gummibeer\Backuplay\Contracts;

/**
 * Interface ParserContract.
 */
interface ParserContract
{
    /**
     * @param string $string
     * @return string
     */
    public function parse($string);

    /**
     * @return string
     */
    public function __toString();
}
