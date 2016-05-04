<?php

namespace Gummibeer\Backuplay\Artisan;

use Illuminate\Console\Command;

class CreateBackup extends Command
{
    protected $name = 'backup:create';

    protected $description = 'Create and store a new backup';

    public function fire()
    {
    }
}
