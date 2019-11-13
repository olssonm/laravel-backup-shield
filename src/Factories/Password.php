<?php

namespace Olssonm\BackupShield\Factories;

use Illuminate\Support\Collection;

use Olssonm\BackupShield\Encryption;
use PhpZip\ZipFile;
use \ZipArchive;

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
            $this->makeZipArchive($encryption, $path);
        }

        // Fall back on PHP-driven ZipFile
        else {
            $this->makeZipFile($encryption, $path);
        }

        consoleOutput()->info('Successfully applied password and encryption to zip.');
    }

    /**
     * Use native PHP ZipArchive
     *
     * @param   Encryption $encryption
     * @return  void
     */
    protected function makeZipArchive(Encryption $encryption, string $path) : void
    {
        $encryptionConstant = $encryption->getEncryptionConstant(
            config('backup-shield.encryption'),
            'ZipArchive'
        );

        $password = config('backup-shield.password');

        $zipArchive = new ZipArchive;

        $zipArchive->open($path, ZipArchive::OVERWRITE);
        $zipArchive->addFile($path, 'backup.zip');
        $zipArchive->setPassword(config('backup-shield.password'));
        Collection::times($zipArchive->numFiles, function ($i) use ($zipArchive, $encryptionConstant) {
            $zipArchive->setEncryptionIndex($i - 1, $encryptionConstant);
        });
        $zipArchive->close();

        $this->path = $path;
    }

    /**
     * Use PhpZip\ZipFile-package to create the zip
     *
     * @param   Encryption $encryption
     * @return  void
     */
    protected function makeZipFile(Encryption $encryption, string $path) : void
    {
        $encryptionConstant = $encryption->getEncryptionConstant(
            config('backup-shield.encryption'),
            'ZipFile'
        );

        $zipFile = new ZipFile();
        $zipFile->addFile($path, 'backup.zip', ZipFile::METHOD_DEFLATED);
        $zipFile->setPassword(config('backup-shield.password'), $encryptionConstant);
        $zipFile->saveAsFile($path);
        $zipFile->close();

        $this->path = $path;
    }
}
