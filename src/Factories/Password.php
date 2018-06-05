<?php

namespace Olssonm\BackupShield\Factories;

use PhpZip\ZipFile;

class Password
{
    /**
     * Path to .zip-fil
     * @var string
     */
    public $path;

    /**
     * Read the .zip, apply password and encryption, then rewrite the file
     * @param string $path the path to the .zip-file
     */
    function __construct(string $path)
    {
        consoleOutput()->info('Applying password and encryption to zip...');

        // Create a new zip, add the zip from spatie/backup, encrypt and resave/overwrite
        $zipFile = new ZipFile();
        $zipFile->addFile($path, 'backup.zip', ZipFile::METHOD_DEFLATED);
        $zipFile->setPassword(config('backup-shield.password'), config('backup-shield.encryption'));
        $zipFile->saveAsFile($path);
        $zipFile->close();

        consoleOutput()->info('Successfully applied password and encryption to zip.');

        $this->path = $path;
    }
}
