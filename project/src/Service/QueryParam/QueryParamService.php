<?php

namespace App\Service\QueryParam;

use Generator;
use App\Service\QueryParam\Order\OrderService;
use App\Service\QueryParam\Filter\FilterService;
use Symfony\Component\HttpFoundation\RequestStack;

class QueryParamService
{
    // ...test?f[company][id][eq]=10&f[employee][first_name][eq]=John&f[employee][last_name][eq]=Smith&s[company][name]=desc
    private ?FilterService $filterService = null;
    private ?OrderService $orderService = null;

    public function __construct(private readonly RequestStack $requestStack)
    {
        $this->decodeRequest();
    }

    public function getFilters(): Generator
    {
        if ($this->filterService instanceof FilterService) {
            yield from $this->filterService->getFilters();
        }
    }

    public function getOrders(): Generator
    {
        if ($this->orderService instanceof OrderService) {
            yield from $this->orderService->getOrders();
        }
    }

    private function decodeRequest(): void
    {
        $requestData = $this->requestStack->getCurrentRequest()->query->all();
        foreach ($requestData as $type => $item) {
            match ($type) {
                'f' => $this->filterService = FilterService::init($item),
                'o' => $this->orderService = OrderService::init($item),
                default => null
            };
        }
    }
}
