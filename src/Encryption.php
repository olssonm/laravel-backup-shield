<?php

namespace Olssonm\BackupShield;

use PhpZip\ZipFile;

use ZipArchive;

class Encryption
{
    const ENCRYPTION_DEFAULT = 'default';
    const ENCRYPTION_WINZIP_AES_128 = 'aes_128';
    const ENCRYPTION_WINZIP_AES_192 = 'aes_192';
    const ENCRYPTION_WINZIP_AES_256 = 'aes_256';

    private $zipArchiveOptions = [
        'default' => ZipArchive::EM_AES_128,
        'aes_128' => ZipArchive::EM_AES_128,
        'aes_192' => ZipArchive::EM_AES_192,
        'aes_256' => ZipArchive::EM_AES_256,
    ];

    private $zipFileOptions = [
        'default' => ZipFile::ENCRYPTION_METHOD_TRADITIONAL,
        'aes_128' => ZipFile::ENCRYPTION_METHOD_WINZIP_AES_128,
        'aes_192' => ZipFile::ENCRYPTION_METHOD_WINZIP_AES_192,
        'aes_256' => ZipFile::ENCRYPTION_METHOD_WINZIP_AES_256,
    ];

    public function getEncryptionConstant($type, $engine)
    {
        if ($engine == 'ZipArchive' && in_array($type, $this->zipArchiveOptions)) {
            return $this->zipArchiveOptions[$type];
        } elseif ($engine == 'ZipFile' && in_array($type, $this->zipFileOptions)) {
            return $this->zipFileOptions[$type];
        }

        throw new \Exception("Encryption key not set or invalid value", 1);
    }
}
