<?php

namespace App\Exception;

use Exception;
use Throwable;

final class CustomException extends Exception implements ResponseExceptionInterface
{
    private ?CustomExceptionDataDto $errorMessageData;

    public function __construct(string $message, int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->errorMessageData = new CustomExceptionDataDto($message, $code);
    }

    public function getCustomMessage(): string
    {
        return $this->errorMessageData->getMessage();
    }
    public function getCustomCode(): int
    {
        return $this->errorMessageData->getCode();
    }
}
