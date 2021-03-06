<?php

namespace Astrotomic\Backuplay\Events;

use Astrotomic\Backuplay\Artisan\CreateBackup;

class BackupCreateBeforeCommand extends Event
{
    /**
     * @var \Astrotomic\Backuplay\Artisan\CreateBackup
     */
    public $command;

    public function __construct(CreateBackup $command)
    {
        $this->command = $command;
    }
}
