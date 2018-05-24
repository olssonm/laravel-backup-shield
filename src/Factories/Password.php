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
        $zip = (new ZipFile())->openFile($path);
        $zip->setPassword(config('backup-shield.password'), config('backup-shield.encryption'));
        $zip->saveAsFile($path);

        $this->path = $path;
    }
}
