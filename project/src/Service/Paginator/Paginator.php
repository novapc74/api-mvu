<?php

namespace App\Service\Paginator;

class Paginator implements PaginatorInterface
{
    private int $count = 0;
    private array $items = [];
    public function __construct(
        private readonly PaginatorRequestDto  $requestDto,
        private readonly PaginatorResponseDto $responseDto,
    )
    {
    }

    public function getPage(): int
    {
        return $this->requestDto->getPage();
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getLimit(): int
    {
        return $this->requestDto->getLimit();
    }

    public function paginate(array $collection): array
    {
        $page = $this->getPage();
        $limit = $this->getLimit();
        $offset = ($page - 1) * $limit;

        $this->items = array_slice($collection, $offset, $limit);

        // Записываем результаты в response DTO
        $this->count = count($collection);

        return $this->responseDto::response($this);
    }
}
