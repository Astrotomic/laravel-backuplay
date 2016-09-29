<?php

namespace Astrotomic\Backuplay;

use Astrotomic\Backuplay\Artisan\CreateBackup;
use Astrotomic\Backuplay\Artisan\ListBackup;
use Astrotomic\Backuplay\Contracts\ConfigContract;
use Astrotomic\Backuplay\Repositories\Config;
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
        $this->registerConfig();
        $this->registerCommands();
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config/backuplay.php' => config_path('backuplay.php'),
        ]);

        $this->mergeConfigFrom(__DIR__.'/../config/backuplay.php', 'backuplay');
    }

    protected function registerCommands()
    {
        $this->app->singleton('backuplay.artisan.backup-create', function () {
            return new CreateBackup();
        });
        $this->app->singleton('backuplay.artisan.backup-list', function () {
            return new ListBackup();
        });

        $this->commands([
            'backuplay.artisan.backup-create',
            'backuplay.artisan.backup-list',
        ]);
    }

    protected function registerConfig()
    {
        $this->app->singleton(ConfigContract::class, function () {
            return new Config();
        });
    }
}
