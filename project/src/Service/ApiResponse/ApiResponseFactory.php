<?php

namespace App\Service\ApiResponse;

use Throwable;
use App\Exception\CustomException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ApiResponseFactory
{
    /**
     *
     * Метод конвертирует массив в строку.
     * Сделано для корректного вывода сообщений на кириллице.
     *
     * @param array $data
     * @return string
     */
    private static function stringifyData(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Формирует успешный ответ с данными.
     *
     * @param array $data
     * @param int $status HTTP-статус (по умолчанию 200)
     * @return JsonResponse
     */
    public static function successResponse(array $data, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(self::stringifyData(
            [
                'success' => true,
                'data' => $data,
            ]
        ), $status, [], true);
    }

    /**
     * Формирует ответ с ошибкой.
     *
     * @param string $message Сообщение ошибки
     * @param int $code Код ошибки (например, из исключения)
     * @param string $type Тип ошибки (например, 'validation_error', 'internal_server_error')
     * @param int $status HTTP-статус (по умолчанию 500 для исключений)
     * @return JsonResponse
     */
    public static function errorResponse(string $message, int $code = 0, string $type = 'internal_server_error', int $status = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return new JsonResponse(self::stringifyData(
            [
                'success' => false,
                'error' => [
                    'message' => $message,
                    'code' => $code,
                    'type' => $type,
                ],
            ]
        ), $status, [], true);
    }

    /**
     * Формирует ответ с ошибкой на основе Throwable.
     *
     * @param CustomException $exception
     * @param int $status HTTP-статус (по умолчанию 500)
     * @return JsonResponse
     */
    public static function exceptionResponse(CustomException $exception, int $status = Response::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        return self::errorResponse(
            $exception->message(),
            $exception->code(),
            $exception->type(),
            $exception->code() ?? $status
        );
    }

    /**
     * Универсальный помощник для формирования ответа.
     * Возвращает JsonResponse на основе типа данных.
     *
     * @param mixed $data Данные для ответа
     * @param int $successStatus Статус для успешного ответа (по умолчанию 200)
     * @param int $errorStatus Статус для ошибки (по умолчанию 500)
     * @return Response
     */
    public static function responseHelper(mixed $data, int $successStatus = Response::HTTP_OK, int $errorStatus = Response::HTTP_INTERNAL_SERVER_ERROR): Response
    {
        if ($data instanceof CustomException) {
            return self::exceptionResponse($data, $errorStatus);
        }

        if ($data instanceof Response) {
            return $data;
        }

        if (is_array($data)) {
            return self::successResponse($data, $successStatus);
        }

        if ($data instanceof Throwable) {
            return self::errorResponse($data->getMessage(), $data->getCode() ?? Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return self::errorResponse('Unsupported data type', 500);
    }
}
