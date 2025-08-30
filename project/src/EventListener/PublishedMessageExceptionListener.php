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
                    'message' => $exception->message(),
                    'code' => $exception->code(),
                    'type' => $exception->type(),
                ]
            ];

            $response = new JsonResponse($data, $exception->code());

            $event->setResponse($response);
        }
    }
}
