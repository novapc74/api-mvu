<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use App\Service\ApiResponse\ApiResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        // Получаем строку контроллера, например: "App\Controller\CartController::someMethod"
        $controller = $request->attributes->get('_controller');

        if (!is_string($controller) || !str_starts_with($controller, 'App\Controller\CartController')) {
            return;
        }

        $exception = $event->getThrowable();

        $response = new JsonResponse(
            ApiResponseFactory::errorResponse($exception),
            $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_BAD_REQUEST,
            [],
            true
        );

        $event->setResponse($response);
    }
}
