<?php

namespace Gummibeer\Backuplay\Events;

use Gummibeer\Backuplay\Artisan\CreateBackup;
use Illuminate\Queue\SerializesModels;

class BackupCreateAfterScripts extends Event
{

    /**
     * @var \Gummibeer\Backuplay\Artisan\CreateBackup
     */
    public $command;
    /**
     * @var string
     */
    public $key;
    /**
     * @var array
     */
    public $scripts;
    /**
     * @var bool
     */
    public $success;

    public function __construct(CreateBackup $command, $key, array $scripts, $success)
    {
        $this->command = $command;
        $this->key = $key;
        $this->scripts = $scripts;
        $this->success = $success;
    }
}
