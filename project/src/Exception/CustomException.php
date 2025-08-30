<?php

namespace App\Exception;

use Exception;
use Throwable;

final class CustomException extends Exception implements ResponseExceptionInterface
{
    private ?CustomExceptionDataDto $errorMessageData;

    public function __construct(string $message, string $type, int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->errorMessageData = new CustomExceptionDataDto($message, $type, $code);
    }

    public function message(): string
    {
        return $this->errorMessageData->getMessage();
    }
    public function code(): int
    {
        return $this->errorMessageData->getCode();
    }

    public function type(): string
    {
        return $this->errorMessageData->getType();
    }
}
