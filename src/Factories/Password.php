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
        consoleOutput()->comment('Applying password and encryption to zipped file...');

        // Create a new zip, add the existing from spatie/backup and encrypt
        $zipFile = new ZipFile();
        $zipFile->addFile($path, 'backup.zip');
        $zipFile->setPassword(config('backup-shield.password'), config('backup-shield.encryption'));
        $zipFile->saveAsFile($path);
        $zipFile->close();

        // $zip = (new ZipFile())->openFile($path);
        // $zip->setPassword(config('backup-shield.password'), config('backup-shield.encryption'));
        // $zip->saveAsFile($path);

        consoleOutput()->comment('Applied password and encryption to zipped file.');

        $this->path = $path;
    }
}
