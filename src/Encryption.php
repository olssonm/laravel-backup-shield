<?php

namespace Olssonm\BackupShield;

use ZipArchive;

class Encryption
{
    /**
     * Default encryption contants
     *
     * @var string
     */
    const ENCRYPTION_DEFAULT = ZipArchive::EM_AES_128;

    /**
     * AES-128 encryption contants
     *
     * @var string
     */
    const ENCRYPTION_WINZIP_AES_128 = ZipArchive::EM_AES_128;

    /**
     * AES-192 encryption contants
     *
     * @var string
     */
    const ENCRYPTION_WINZIP_AES_192 = ZipArchive::EM_AES_192;

    /**
     * AES-256 encryption contants
     *
     * @var string
     */
    const ENCRYPTION_WINZIP_AES_256 = ZipArchive::EM_AES_256;
}
