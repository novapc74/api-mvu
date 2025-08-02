<?php

namespace App\Service\ApiResponse;

use Throwable;

final readonly class ApiResponseFactory
{
    /**
     * Формирует успешный ответ с данными.
     *
     * @param array $data
     * @return array<string, mixed>
     */
    public static function successResponse(array $data): array
    {
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    /**
     * Формирует ответ с ошибкой на основе исключения.
     *
     * @return array<string, mixed>
     */
    public static function errorResponse(Throwable $exception): array
    {
        return [
            'success' => false,
            'error' => [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ],
        ];
    }
}
