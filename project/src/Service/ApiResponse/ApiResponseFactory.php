<?php

namespace App\Service\ApiResponse;

use Throwable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ApiResponseFactory
{
    /**
     * Формирует успешный ответ с данными.
     *
     * @param array $data
     * @return string
     */
    public static function successResponse(array $data): string
    {
        $data = [
            'success' => true,
            'data' => $data,
        ];

        return self::stringifyData($data);
    }

    /**
     * Формирует ответ с ошибкой на основе исключения.
     *
     * @param Throwable $exception
     * @return string
     */
    public static function errorResponse(Throwable $exception): string
    {
        $data = [
            'success' => false,
            'error' => [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ],
        ];

        return self::stringifyData($data);
    }

    private static function stringifyData(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public static function responseHelper(Throwable|array $data): JsonResponse
    {
        if ($data instanceof Throwable) {

            return new JsonResponse(
                self::errorResponse($data),
                $data->getCode() ?: Response::HTTP_BAD_REQUEST,
                [],
                true
            );
        }

        return new JsonResponse(
            self::successResponse($data),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
