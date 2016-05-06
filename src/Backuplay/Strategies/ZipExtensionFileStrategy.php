<?php

namespace Gummibeer\Backuplay\Strategies;

use Alchemy\Zippy\Adapter\ZipExtensionAdapter;
use Alchemy\Zippy\FileStrategy\AbstractFileStrategy;

class ZipExtensionFileStrategy extends AbstractFileStrategy
{
    /**
     * {@inheritdoc}
     */
    protected function getServiceNames()
    {
        return array(
            ZipExtensionAdapter::class
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFileExtension()
    {
        return 'ziphp';
    }
}
