<?php

namespace App\Service\Cart;

use App\Service\Crypto\Cryptography;

readonly class CartHash
{
    public function __construct(private string $cartSalt)
    {
    }

    public function encodeHash(string $hash): string
    {
        return Cryptography::encrypt($this->cartSalt, $hash);
    }

    public function decodeHash(string $hash): string
    {
        return Cryptography::decrypt($this->cartSalt, $hash);
    }
}
