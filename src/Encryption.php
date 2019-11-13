<?php

namespace Olssonm\BackupShield;

use PhpZip\ZipFile;

use \ZipArchive;

class Encryption
{
    /**
     * Default encryption contants
     *
     * @var string
     */
    const ENCRYPTION_DEFAULT = 'default';

    /**
     * AES-128 encryption contants
     *
     * @var string
     */
    const ENCRYPTION_WINZIP_AES_128 = 'aes_128';

    /**
     * AES-192 encryption contants
     *
     * @var string
     */
    const ENCRYPTION_WINZIP_AES_192 = 'aes_192';

    /**
     * AES-256 encryption contants
     *
     * @var string
     */
    const ENCRYPTION_WINZIP_AES_256 = 'aes_256';

    /**
     * ZipArchive encryption constants; stores as simple string for PHP < 7.2
     * backwards compatability
     *
     * @var array
     */
    private $zipArchiveOptions = [
        self::ENCRYPTION_DEFAULT => '257',
        self::ENCRYPTION_WINZIP_AES_128 => '257',
        self::ENCRYPTION_WINZIP_AES_192 => '258',
        self::ENCRYPTION_WINZIP_AES_256 => '259',
    ];

    /**
     * ZipFile encryption constants
     *
     * @var array
     */
    private $zipFileOptions = [
        self::ENCRYPTION_DEFAULT => ZipFile::ENCRYPTION_METHOD_TRADITIONAL,
        self::ENCRYPTION_WINZIP_AES_128 => ZipFile::ENCRYPTION_METHOD_WINZIP_AES_128,
        self::ENCRYPTION_WINZIP_AES_192 => ZipFile::ENCRYPTION_METHOD_WINZIP_AES_192,
        self::ENCRYPTION_WINZIP_AES_256 => ZipFile::ENCRYPTION_METHOD_WINZIP_AES_256,
    ];

    /**
     * Retrive appropriate encryption constant
     *
     * @param  string $type
     * @param  string $engine
     * @return mixed
     */
    public function getEncryptionConstant($type, $engine)
    {
        if ($engine == 'ZipArchive' && isset($this->zipArchiveOptions[$type])) {
            return $this->zipArchiveOptions[$type];
        } elseif ($engine == 'ZipFile' && isset($this->zipFileOptions[$type])) {
            return $this->zipFileOptions[$type];
        } else {
            throw new \Exception("Encryption key not set or invalid value", 1);
        }
    }
}
