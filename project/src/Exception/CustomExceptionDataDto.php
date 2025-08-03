<?php

namespace App\Exception;

final readonly class CustomExceptionDataDto
{
    public function __construct(private string $message, private int $code)
    {
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
