<?php

namespace App\Service\Paginator;


class PaginatorResponseDto
{
    public static function response(PaginatorInterface $paginator): array
    {
        return [
            'paginator' => [
                'page' => $paginator->getPage(),
                'limit' => $paginator->getLimit(),
                'count' => $paginator->getCount(),
                'pagesCount' => $paginator->getPagesCount(),
            ],
            'items' => $paginator->getItems(),
        ];
    }
}
