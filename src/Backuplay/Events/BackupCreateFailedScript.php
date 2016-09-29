<?php

namespace Astrotomic\Backuplay\Events;

use Astrotomic\Backuplay\Artisan\CreateBackup;
use Symfony\Component\Process\Process;

class BackupCreateFailedScript extends Event
{
    /**
     * @var \Astrotomic\Backuplay\Artisan\CreateBackup
     */
    public $command;
    /**
     * @var string
     */
    public $script;
    /**
     * @var \Symfony\Component\Process\Process
     */
    public $process;

    public function __construct(CreateBackup $command, $script, Process $process)
    {
        $this->command = $command;
        $this->script = $script;
        $this->process = $process;
    }
}
