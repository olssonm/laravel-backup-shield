<?php

namespace Olssonm\BackupShield;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use Spatie\Backup\Events\BackupZipWasCreated;
use Olssonm\BackupShield\Listeners\PasswordProtectZip;

/**
 * Laravel service provider for the Backup Shield-package
 */
class BackupShieldServiceProvider extends ServiceProvider
{
    /**
     * Config-path
     * @var string
     */
    protected $config;

    /**
     * Constructor
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct($app) {
        $this->config = __DIR__ . '/config/backup-shield.php';

        parent::__construct($app);
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot() : void
    {
        // Publishing of configuration
        $this->publishes([
            $this->config => config_path('backup-shield.php'),
        ]);

        // Listen for the "BackupZipWasCreated" event
        Event::listen(
            BackupZipWasCreated::class,
            PasswordProtectZip::class
        );

        parent::boot();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register() : void
    {
        $this->mergeConfigFrom(
            $this->config, 'backup-shield'
        );
    }
}
