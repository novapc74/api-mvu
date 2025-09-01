<?php

namespace App\Service\Product\Adapter;

use App\Entity\Cart;
use App\Entity\Product;
use App\Service\Paginator\PaginatorInterface;
use App\Service\QueryParam\QueryParamInterface;

final class ProductCatalogSqlDto implements SqlDoctrineInterface
{
    private static ?PaginatorInterface $paginator;
    private static ?Cart $cart;
    private static ?QueryParamInterface $queryData;

    public static function init(
        PaginatorInterface  $paginator = null,
        Cart                $cart = null,
        QueryParamInterface $queryData = null
    ): self
    {
        $instance = new self();

        $instance::$paginator = $paginator;
        $instance::$cart = $cart;
        $instance::$queryData = $queryData;

        return $instance;
    }

    public function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function getSql(): string
    {
        $sql = "SELECT CONCAT(
            SUBSTR(HEX(p.id), 1, 8), '-',
            SUBSTR(HEX(p.id), 9, 4), '-',
            SUBSTR(HEX(p.id), 13, 4), '-',
            SUBSTR(HEX(p.id), 17, 4), '-',
            SUBSTR(HEX(p.id), 21, 12)
        ) AS id, p.name, p.slug";

        if (self::$cart !== null) {
            $sql .= ", COALESCE(ci.quantity, 0) AS quantity";
        }

        $sql .= " FROM product p";

        if (self::$cart !== null) {
            $sql .= " LEFT JOIN cart_item ci ON ci.product_id = p.id AND ci.cart_id = UNHEX(REPLACE(:cartId, '-', ''))";
        }

        if (self::$queryData) {
            #TODO add filters & orders
        }

        self::$paginator?->paginateSql($sql);

        return $sql;
    }

    public function getParams(): array
    {
        return self::$cart
            ? ['cartId' => self::$cart->getId()->toRfc4122()]
            : [];
    }

    public function getTypes(): array
    {
        return [
            'cartId' => 'string'
        ];
    }
}
