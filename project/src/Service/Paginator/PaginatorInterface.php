<?php

namespace App\Service\Paginator;

use Doctrine\ORM\QueryBuilder;

interface PaginatorInterface
{
    public function getPage(): int;

    public function getItems(): array;

    public function getCount(): int;

    public function getLimit(): int;

    public function getOffset(): int;

    public function getPagesCount(): int;

    public function paginateQueryBuilder(QueryBuilder $queryBuilder): void;

    public function paginateSql(string &$sql): void;
}
