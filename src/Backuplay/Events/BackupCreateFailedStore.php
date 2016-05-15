<?php

namespace Gummibeer\Backuplay\Events;

use Gummibeer\Backuplay\Artisan\CreateBackup;
use Illuminate\Queue\SerializesModels;

class BackupCreateFailedStore extends Event
{
    use SerializesModels;

    /**
     * @var \Gummibeer\Backuplay\Artisan\CreateBackup
     */
    public $command;
    /**
     * @var string
     */
    public $cycle;
    /**
     * @var string
     */
    public $filePath;
    /**
     * @var string
     */
    public $content;

    public function __construct(CreateBackup $command, $cycle, $filePath, $content)
    {
        $this->command = $command;
        $this->cycle = $cycle;
        $this->filePath = $filePath;
        $this->content = $content;
    }
}
