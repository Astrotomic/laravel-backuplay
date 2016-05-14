<?php

namespace Gummibeer\Backuplay\Contracts;

interface ConfigContract
{
    /**
     * @return array
     */
    public function getFolders();

    /**
     * @return array
     */
    public function getFiles();

    /**
     * @param bool|null $strict
     * @return bool
     */
    public function isStrict($strict = null);

    /**
     * @return string
     * @throws \Gummibeer\Backuplay\Exceptions\EntityIsNoDirectoryException
     */
    public function getTempDir();
}
