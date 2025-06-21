<?php

namespace App\Trait;

use App\Service\Crypto\Cryptography;

trait CryptographyTrait
{
    public static function encodeHash(string $salt, string $hash): string
    {
        return Cryptography::encrypt($salt, $hash);
    }

    public static function decodeHash(string $salt, string $hash): string
    {
        return Cryptography::decrypt($salt, $hash);
    }
}
