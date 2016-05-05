<?php

namespace Gummibeer\Backuplay;

use Gummibeer\Backuplay\Artisan\CreateBackup;
use Illuminate\Support\ServiceProvider;

/**
 * Class BackuplayServiceProvider.
 */
class BackuplayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->artisan();
    }

    public function boot()
    {
        $this->config();
    }

    protected function config()
    {
        $this->publishes([
            __DIR__.'/../config/backuplay.php' => config_path('backuplay.php'),
        ]);

        $this->mergeConfigFrom(__DIR__.'/../config/backuplay.php', 'backuplay');
    }

    protected function artisan()
    {
        $this->app->singleton('backuplay.artisan.backup-create', function () {
            return new CreateBackup();
        });

        $this->commands([
            'backuplay.artisan.backup-create',
        ]);
    }
}
