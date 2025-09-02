<?php

namespace App\Service\Paginator;

use Symfony\Component\HttpFoundation\Request;

readonly class PaginatorRequestDto
{
    private const LIMIT_PER_PAGE = 4    ;

    public function __construct(
        private int $page,
        private int $limit,
    )
    {
    }

    public static function fromRequest(Request $request): self
    {
        $pageFromRequest = $request->get('page', 1);
        $page = max($pageFromRequest, 1);

        $limitFromRequest = $request->get('limit', self::LIMIT_PER_PAGE);
        $limit = min(max($limitFromRequest, 1), 48);

        return new self($page, $limit);
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
