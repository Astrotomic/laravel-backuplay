<?php

namespace Gummibeer\Backuplay\Helpers;

use Alchemy\Zippy\Adapter\AdapterContainer;
use Alchemy\Zippy\Zippy;
use Gummibeer\Backuplay\Strategies\ZipExtensionFileStrategy;

class Archive
{
    public static function load()
    {
        $zippy = Zippy::load();
        $adapters = AdapterContainer::load();
        $zippy->addStrategy(new ZipExtensionFileStrategy($adapters));

        return $zippy;
    }
}
