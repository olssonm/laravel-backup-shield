<?php

namespace Olssonm\BackupShield\Factories;

use Illuminate\Support\Collection;
use PhpZip\ZipFile;
use Olssonm\BackupShield\Encryption;

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
    function __construct(Encryption $encryption, string $path)
    {
        consoleOutput()->info('Applying password and encryption to zip...');

        // If ZipArchive is enabled
        if (class_exists('ZipArchive') && version_compare(PHP_VERSION, '7.2.0') >= 0) {
            dd('!');
            $encryptionConstant = $encryption->getEncryptionConstant(
                config('backup-shield.encryption'),
                'ZipArchive'
            );

            $password = config('backup-shield.password');

            $zipArchive = new \ZipArchive;

            $zipArchive->open($path, \ZipArchive::OVERWRITE);
            $zipArchive->addFile($path, 'backup.zip');
            $zipArchive->setPassword(config('backup-shield.password'));
            Collection::times($zipArchive->numFiles, function ($i) use ($zipArchive, $encryptionConstant) {
                $zipArchive->setEncryptionIndex($i - 1, $encryptionConstant);
            });
            $zipArchive->close();
        }

        // Fall back on PHP-driven ZipFile
        else {
            $encryptionConstant = $encryption->getEncryptionConstant(
                config('backup-shield.encryption'),
                'ZipFile'
            );

            dd($encryptionConstant);

            $zipFile = new ZipFile();
            $zipFile->addFile($path, 'backup.zip', ZipFile::METHOD_DEFLATED);
            $zipFile->setPassword(config('backup-shield.password'), $encryptionConstant);
            $zipFile->saveAsFile($path);
            $zipFile->close();
        }

        consoleOutput()->info('Successfully applied password and encryption to zip.');

        $this->path = $path;
    }
}
