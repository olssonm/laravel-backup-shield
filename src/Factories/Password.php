<?php

namespace Olssonm\BackupShield\Factories;

use Illuminate\Support\Collection;
use Olssonm\BackupShield\Encryption;

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
     * @param string     $path
     */
    function __construct(string $path)
    {
        $this->password = config('backup-shield.password');

        // If no password is set, just return the backup-path
        if (!$this->password) { 
            return $this->path = $path;
        }

        consoleOutput()->info('Applying password and encryption to zip using ZipArchive...');
        
        $this->makeZip($path);

        consoleOutput()->info('Successfully applied password and encryption to zip.');
    }

    /**
     * Use native PHP ZipArchive
     *
     * @return  void
     */
    protected function makeZip(string $path): void
    {
        $encryption = config('backup-shield.encryption');

        $zipArchive = new ZipArchive;

        $zipArchive->open($path, ZipArchive::OVERWRITE);
        $zipArchive->addFile($path, 'backup.zip');
        $zipArchive->setPassword($this->password);
        Collection::times($zipArchive->numFiles, function ($i) use ($zipArchive, $encryption) {
            $zipArchive->setEncryptionIndex($i - 1, $encryption);
        });
        $zipArchive->close();

        $this->path = $path;
    }
}
