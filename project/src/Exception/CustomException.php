<?php

namespace App\Exception;

use Exception;
use Throwable;

class CustomException extends Exception implements ResponseExceptionInterface
{
    private array $errorMessageData = [];

    public function __construct(string $message, int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->errorMessageData['message'] = $message;
        $this->errorMessageData['code'] = $code;
    }

    public function getExceptionMessage(): array
    {
        return $this->errorMessageData ?? [];
    }
}