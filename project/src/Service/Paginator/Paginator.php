<?php

namespace App\Service\Paginator;

use Doctrine\ORM\QueryBuilder;

class Paginator implements PaginatorInterface
{
    private int $count = 0;
    private array $items = [];

    public function __construct(
        private readonly PaginatorRequestDto  $requestDto,
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

    public function getOffset(): int
    {
        return ($this->getPage() - 1) * $this->getLimit();
    }

    public function paginate(array $collection, ?int $count = null): self
    {
        if ($count === null) {
            $offset = $this->getOffset();
            $limit = $this->getLimit();

            $this->items = array_slice($collection, $offset, $limit);
            $this->count = count($collection);
        } else {
            $this->items = $collection;
            $this->count = $count;
        }

        return $this;
    }

    public function paginateQueryBuilder(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->setMaxResults($this->getLimit())
            ->setFirstResult($this->getOffset());
    }

    public function paginateSql(string &$sql): void
    {
        $sql .= " LIMIT {$this->getLimit()} OFFSET {$this->getOffset()}";
    }
}
