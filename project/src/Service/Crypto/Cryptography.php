<?php

namespace App\Service\Crypto;

use Symfony\Component\String\ByteString;

class Cryptography
{
    public static function encrypt(string $salt, string $hash): string
    {
        $encrypted = openssl_encrypt($hash, 'AES-256-CBC', $salt, 0, self::getFixedVector($salt));

        return base64_encode($encrypted);
    }

    public static function decrypt(string $salt, string $hash): string
    {
        $encryptedData = base64_decode($hash);

        return openssl_decrypt($encryptedData, 'AES-256-CBC', $salt, 0, self::getFixedVector($salt));
    }

    private static function getFixedVector(string $encryptionKey): string
    {
        return substr(sha1($encryptionKey), 0, openssl_cipher_iv_length('AES-256-CBC'));
    }

    public static function genHash(): string
    {
        return ByteString::fromRandom()->toString();
    }
}
