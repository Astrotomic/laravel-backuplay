<?php

namespace Gummibeer\Backuplay\Events;

use Gummibeer\Backuplay\Artisan\CreateBackup;

class BackupCreateAfterCommand extends Event
{
    /**
     * @var \Gummibeer\Backuplay\Artisan\CreateBackup
     */
    public $command;

    public function __construct(CreateBackup $command)
    {
        $this->command = $command;
    }
}
