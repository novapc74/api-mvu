<?php

namespace App\Trait;

trait ArrayToStringEncoderTrait
{
    public static function toString(array $array, int $depth = 10): string
    {
        return json_encode($array, JSON_UNESCAPED_UNICODE, $depth);
    }
}