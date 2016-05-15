<?php
namespace Gummibeer\Backuplay\Events;

use Gummibeer\Backuplay\Artisan\CreateBackup;
use Illuminate\Queue\SerializesModels;

class BackupCreateAfterScripts extends Event
{
    use SerializesModels;

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

    public function __construct(CreateBackup $command, $key, array $scripts, $success)
    {
        $this->command = $command;
        $this->key = $key;
        $this->scripts = $scripts;
    }
}
