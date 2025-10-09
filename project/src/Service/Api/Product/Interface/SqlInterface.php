<?php

namespace App\Service\Api\Product\Interface;

interface SqlInterface
{
    public static function init(mixed $data): self;
    public function getSql(): string;
    public function getParam(): array;
    public function getType(): array;
}
