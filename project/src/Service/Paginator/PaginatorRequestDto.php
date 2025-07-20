<?php

namespace App\Service\Paginator;


use Symfony\Component\HttpFoundation\RequestStack;

class PaginatorRequestDto
{
    private int $page;
    private int $limit;

    public function __construct(
        private readonly RequestStack $requestStack,
    )
    {
        $request = $this->requestStack->getCurrentRequest();
        $this->page = max($request->get('page', 1), 1);
        $this->limit = $request->get('limit', 24);
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
