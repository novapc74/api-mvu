<?php

namespace App\Middleware;

use App\Exception\CustomException;
use App\Service\ApiResponse\FetchRequestValidator;
use Symfony\Component\HttpKernel\Event\RequestEvent;

readonly final class CartFetchRequestValidatorMiddleware
{
    public function __construct(private FetchRequestValidator $fetchRequestValidator)
    {
    }

    /**
     * @throws CustomException
     */
    public function __invoke(RequestEvent $event): void
    {

        $request = $event->getRequest();

        $route = $request->attributes->get('_route');

        if ($request->isXmlHttpRequest()) {
            return;
        }

        if (!$route || !str_starts_with($route, 'api_')) {
            return;
        }

        $this->fetchRequestValidator->validateFetchRequest($request);
    }
}
