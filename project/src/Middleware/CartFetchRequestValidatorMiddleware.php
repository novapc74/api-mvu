<?php

namespace App\Middleware;

use App\Service\ApiResponse\FetchRequestValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

readonly final class CartFetchRequestValidatorMiddleware
{
    public function __construct(private FetchRequestValidator $fetchRequestValidator)
    {
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $route = $request->attributes->get('_route');

        if (!$route || !str_starts_with($route, 'cart')) {
            return;
        }

        $validationResponse = $this->fetchRequestValidator->validateFetchRequest($request);
        if ($validationResponse instanceof JsonResponse) {
            $event->setResponse($validationResponse);
        }
    }
}
