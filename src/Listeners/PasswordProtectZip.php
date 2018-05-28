<?php

namespace Olssonm\BackupShield\Listeners;

use Olssonm\BackupShield\Factories\Password;

use Spatie\Backup\Events\BackupZipWasCreated;

class PasswordProtectZip
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Spatie\Backup\Events\BackupZipWasCreated  $event
     * @return string
     */
    public function handle(BackupZipWasCreated $event)
    {
        return (new Password($event->pathToZip))->path;
    }
}
