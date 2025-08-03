<?php

namespace App\EventListener;

use App\Exception\ResponseExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class PublishedMessageExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ResponseExceptionInterface) {

            $data = [
                'success' => false,
                'error' => [
                    'message' => $exception->getCustomMessage(),
                    'code' => $exception->getCustomCode(),
                ]
            ];

            $response = new JsonResponse($data, $exception->getCustomCode());

            $event->setResponse($response);
        }
    }
}
