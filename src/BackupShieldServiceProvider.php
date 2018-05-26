<?php

namespace Olssonm\BackupShield;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class BackupShieldServiceProvider extends ServiceProvider
{
    /**
     * Register listener to the "BackupZipWasCreated" event
     * @var array
     */
    protected $listen = [
        'Spatie\Backup\Events\BackupZipWasCreated' => [
            'Olssonm\BackupShield\Listeners\PasswordProtectZip',
        ],
    ];

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
    public function boot()
    {
        // Publishing of configuration
        $this->publishes([
            $this->config => config_path('backup-shield.php'),
        ]);

        parent::boot();
    }

    /**
     * Register any package services.
     * 
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->config, 'backup-shield'
        );
    }
}
