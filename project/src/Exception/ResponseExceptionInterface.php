<?php

namespace App\Exception;

interface ResponseExceptionInterface
{
    public function getCustomMessage(): string;
    public function getCustomCode(): int;
}
