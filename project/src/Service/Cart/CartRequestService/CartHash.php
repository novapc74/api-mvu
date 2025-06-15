<?php

namespace App\Service\Cart\CartRequestService;

use App\Service\Crypto\Cryptography;

class CartHash
{
    public function __construct(private readonly string $cartSalt)
    {
    }

    public static function init(string $cartSalt): CartHash
    {
        return new self($cartSalt);
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
