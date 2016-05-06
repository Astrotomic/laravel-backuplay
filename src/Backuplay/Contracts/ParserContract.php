<?php
namespace Gummibeer\Backuplay\Contracts;


/**
 * Interface ParserContract
 * @package Gummibeer\Backuplay\Contracts
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