<?php

namespace App\Service\Paginator;

interface PaginatorInterface
{
    public function getPage(): int;
    public function getItems(): array;
    public function getCount(): int;
    public function getLimit(): int;
}
