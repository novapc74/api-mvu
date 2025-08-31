<?php

namespace App\EventListener;

use App\Service\ApiResponse\ApiResponseFactory;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ApiExceptionListener
{
    private const CONTROLLER_NAMESPACE = 'App\Controller\Api';

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $controller = $request->attributes->get('_controller');

        if (!is_string($controller) || !str_starts_with($controller, self::CONTROLLER_NAMESPACE)) {
            return;
        }

        $exception = $event->getThrowable();

        $response = ApiResponseFactory::responseHelper($exception);

        $event->setResponse($response);
    }
}
