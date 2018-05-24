<?php

    return [
        'password' => config('app.key'),
        'encryption' => \Olssonm\BackupShield\Encryption::ENCRYPTION_DEFAULT

        // Available encryption methods:
        // ENCRYPTION_DEFAULT (PKWARE/ZipCrypto)
        // ENCRYPTION_WINZIP_AES_128 (AES 128)
        // ENCRYPTION_WINZIP_AES_192 (AES 192)
        // ENCRYPTION_WINZIP_AES_256 (AES 256)
    ];
