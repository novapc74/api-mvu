<?php

namespace App\EventListener;

use App\Trait\ArrayToStringEncoderTrait;
use App\Exception\ResponseExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class PublishedMessageExceptionListener
{
    use ArrayToStringEncoderTrait;

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ResponseExceptionInterface) {

            $messageData = $exception->getExceptionMessage();

            $response = new JsonResponse($this->toString(['success' => false, 'error' => $messageData['message']]), $messageData['code'], [
                'Content-Type' => 'application/json'
            ], true);

            $event->setResponse($response);
        }
    }
}