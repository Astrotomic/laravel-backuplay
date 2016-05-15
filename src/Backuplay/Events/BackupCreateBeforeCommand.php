<?php

namespace Gummibeer\Backuplay\Events;

use Gummibeer\Backuplay\Artisan\CreateBackup;
use Illuminate\Queue\SerializesModels;

class BackupCreateBeforeCommand extends Event
{
    use SerializesModels;

    /**
     * @var \Gummibeer\Backuplay\Artisan\CreateBackup
     */
    public $command;

    public function __construct(CreateBackup $command)
    {
        $this->command = $command;
    }
}
