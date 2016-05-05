<?php

namespace Gummibeer\Backuplay;

use Gummibeer\Backuplay\Artisan\CreateBackup;
use Illuminate\Support\ServiceProvider;

/**
 * Class BackuplayServiceProvider.
 */
class BackuplayServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->artisan();
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
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
