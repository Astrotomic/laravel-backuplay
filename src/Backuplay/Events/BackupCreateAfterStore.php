<?php

namespace Astrotomic\Backuplay\Events;

use Astrotomic\Backuplay\Artisan\CreateBackup;

class BackupCreateAfterStore extends Event
{
    /**
     * @var \Astrotomic\Backuplay\Artisan\CreateBackup
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
