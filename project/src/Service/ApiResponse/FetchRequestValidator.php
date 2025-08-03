<?php

namespace App\Service\ApiResponse;

use App\Exception\CustomException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final readonly class FetchRequestValidator
{
    private const CART_CSRF_TOKEN_NAME = 'cart_csrf_token';

    public function __construct(
        private CsrfTokenManagerInterface $csrfTokenManager,
    )
    {
    }

    public function validateFetchRequest(Request $request): JsonResponse|bool
    {

        if (!$token = $request->headers->get('X-CSRF-Token')) {
            return ApiResponseFactory::responseHelper(
                new CustomException('Отсутствует CSRF-токен.', Response::HTTP_FORBIDDEN)
            );
        }

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken(self::CART_CSRF_TOKEN_NAME, $token))) {
            return ApiResponseFactory::responseHelper(
                new CustomException('Неверный CSRF-токен.', Response::HTTP_FORBIDDEN)
            );
        }

        if (!$request->isXmlHttpRequest()) {
            return ApiResponseFactory::responseHelper(
                new CustomException('Требуется AJAX-запрос.', Response::HTTP_BAD_REQUEST)
            );
        }

        return true;
    }
}
