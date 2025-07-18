<?php

namespace App\Service\Paginator;


class PaginatorResponseDto
{
    public static function response(PaginatorInterface $paginator): array
    {
        return [
            'page' => $paginator->getPage(),
            'limit' => $paginator->getLimit(),
            'count' => $paginator->getCount(),
            'items' => $paginator->getItems(),
        ];
    }
}
