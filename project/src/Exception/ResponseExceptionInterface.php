<?php

namespace App\Exception;

interface ResponseExceptionInterface
{
    public function getExceptionMessage(): array;
}