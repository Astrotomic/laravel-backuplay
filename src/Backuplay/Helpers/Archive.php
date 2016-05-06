<?php

namespace Gummibeer\Backuplay\Helpers;

use Alchemy\Zippy\Adapter\AdapterContainer;
use Alchemy\Zippy\Zippy;
use Gummibeer\Backuplay\Strategies\ZipExtensionFileStrategy;

/**
 * Class Archive.
 */
class Archive
{
    /**
     * @return \Alchemy\Zippy\Zippy
     */
    public static function load()
    {
        $zippy = Zippy::load();
        $adapters = AdapterContainer::load();
        $zippy->addStrategy(new ZipExtensionFileStrategy($adapters));

        return $zippy;
    }
}
