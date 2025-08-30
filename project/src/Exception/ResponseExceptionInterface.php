<?php

namespace App\Exception;

interface ResponseExceptionInterface
{
    public function message(): string;
    public function code(): int;
    public function type(): string;
}
