<?php

namespace App\Exception;

final readonly class CustomExceptionDataDto
{
    public function __construct(
        private string $message,
        private string $type,
        private int    $code
    )
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

    public function getType(): string
    {
        return $this->type;
    }
}
