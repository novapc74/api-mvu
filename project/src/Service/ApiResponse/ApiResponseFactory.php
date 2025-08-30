<?php

namespace App\Service\ApiResponse;

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

    private static function stringifyData(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public static function responseHelper(mixed $data): Response
    {
        if ($data instanceof Response) {
            return $data;
        }

        return new JsonResponse(
            self::successResponse($data),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
