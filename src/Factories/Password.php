<?php

namespace Olssonm\BackupShield\Factories;

use Illuminate\Support\Collection;

use Olssonm\BackupShield\Encryption;
use PhpZip\ZipFile;
use \ZipArchive;

class Password
{
    /**
     * Path to .zip-file
     *
     * @var string
     */
    public $path;

    /**
     * The chosen password
     *
     * @var string
     */
    protected $password;

    /**
     * Read the .zip, apply password and encryption, then rewrite the file
     *
     * @param Encryption $encryption
     * @param string     $path
     */
    function __construct(Encryption $encryption, string $path)
    {
        $this->password = config('backup-shield.password');

        if (!$this->password) {
            return $this->path = $path;
        }

        // If ZipArchive is enabled
        if (class_exists('ZipArchive') && in_array('setEncryptionIndex', get_class_methods('ZipArchive'))) {
            consoleOutput()->info('Applying password and encryption to zip using ZipArchive...');
            $this->makeZipArchive($encryption, $path);
        }

        // Fall back on PHP-driven ZipFile
        else {
            consoleOutput()->info('Applying password and encryption to zip using ZipFile...');
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

        $zipArchive = new ZipArchive;

        $zipArchive->open($path, ZipArchive::OVERWRITE);
        $zipArchive->addFile($path, 'backup.zip');
        $zipArchive->setPassword($this->password);
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
        $zipFile->setPassword($this->password, $encryptionConstant);
        $zipFile->saveAsFile($path);
        $zipFile->close();

        $this->path = $path;
    }
}
