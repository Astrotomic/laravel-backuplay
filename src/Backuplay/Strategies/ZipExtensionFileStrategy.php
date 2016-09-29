<?php

namespace Astrotomic\Backuplay\Strategies;

use Alchemy\Zippy\Adapter\ZipExtensionAdapter;
use Alchemy\Zippy\FileStrategy\AbstractFileStrategy;

class ZipExtensionFileStrategy extends AbstractFileStrategy
{
    /**
     * {@inheritdoc}
     */
    protected function getServiceNames()
    {
        return [
            ZipExtensionAdapter::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFileExtension()
    {
        return 'ziphp';
    }
}
