<?php

namespace App\Service\Api\Product\Dto;

use App\Service\Api\Product\Interface\SqlInterface;
use InvalidArgumentException;

final readonly class ProductsByCategorySlugDto implements SqlInterface
{
    private const CATEGORY_PARAM = 'slug';

    protected function __construct(private string $slug)
    {
    }

    public static function init(mixed $data): SqlInterface
    {
        if (!is_string($data)) {
            throw new InvalidArgumentException('Data must be a string');
        }

        return new self($data);
    }

    public function getSql(): string
    {
        return "-- CTE для складов (фильтруем по товарам категории)
WITH variant_stocks AS (
    SELECT
        pv.id AS variant_id,
        JSON_ARRAYAGG(
            JSON_OBJECT(
                'id', st.id,
                'price', st.price,
                'amount', st.amount,
                'warehouse', JSON_OBJECT('name', w.name, 'address', w.address)
            ) ORDER BY st.warehouse_id  -- Сортировка по ID для скорости
        ) AS stocks
    FROM product_variant pv
    LEFT JOIN stock st ON pv.id = st.product_variant_id
    INNER JOIN warehouse w ON st.warehouse_id = w.id
    WHERE pv.product_id IN (
        SELECT p.id
        FROM product p
        WHERE p.category_id = (SELECT c.id FROM category c WHERE c.slug = :slug)
    )  -- Фильтр по товарам категории
    GROUP BY pv.id
),
-- Подзапрос для свойств (фильтруем по товарам категории)
product_properties AS (
    SELECT
        pp.product_id,
        JSON_ARRAYAGG(
            JSON_OBJECT(
                'name', pr.name,
                'value', pp.value,
                'measure', m.measure
            ) ORDER BY pp.scale
        ) AS properties
    FROM product_property pp
    INNER JOIN property pr ON pr.id = pp.property_id
    INNER JOIN measure m ON m.id = pr.measure_id
    WHERE pp.product_id IN (
        SELECT p.id
        FROM product p
        WHERE p.category_id = (SELECT c.id FROM category c WHERE c.slug = :slug)
    )
    GROUP BY pp.product_id
),
-- Подзапрос для изображений (фильтруем по товарам категории)
product_images AS (
    SELECT
        p.id AS product_id,
        JSON_ARRAYAGG(
            JSON_OBJECT('path', 'https://localhost/upload/images/default.wav', 'is_main', TRUE)
        ) AS images
    FROM product p
    WHERE p.category_id = (SELECT c.id FROM category c WHERE c.slug = :slug)
    GROUP BY p.id
),
-- Подзапрос для агрегации детей (вариантов) по товарам
product_variants AS (
    SELECT
        p.id AS product_id,
        COUNT(pv.id) AS variant_count,
        JSON_ARRAYAGG(
            JSON_OBJECT(
                'variant_id', pv.id,
                'size', JSON_OBJECT('id', s.id, 'name', s.size),
                'gender', JSON_OBJECT('id', g.id, 'name', g.gender),
                'color', JSON_OBJECT('id', c.id, 'name', c.name, 'hex', c.hex_code),
                'stocks', COALESCE(vs.stocks, JSON_ARRAY())
            ) ORDER BY s.id, g.id, c.id  -- Сортировка по популярности для UX
        ) AS children
    FROM product p
    INNER JOIN product_variant pv ON p.id = pv.product_id
    INNER JOIN size s ON pv.size_id = s.id
    INNER JOIN gender g ON pv.gender_id = g.id
    INNER JOIN color c ON pv.color_id = c.id
    LEFT JOIN variant_stocks vs ON pv.id = vs.variant_id
    WHERE p.category_id = (SELECT c.id FROM category c WHERE c.slug = :slug)
    GROUP BY p.id
)
-- Основной запрос: агрегируем товары в массив
SELECT
    JSON_ARRAYAGG(
        JSON_OBJECT(
            'id', LOWER(CONCAT_WS('-', SUBSTR(HEX(p.id), 1, 8), SUBSTR(HEX(p.id), 9, 4), SUBSTR(HEX(p.id), 13, 4), SUBSTR(HEX(p.id), 17, 4), SUBSTR(HEX(p.id), 21))),
            'name', p.name,
            'slug', p.slug,
            'variant_count', pv.variant_count,
            'images', COALESCE(pi.images, JSON_ARRAY()),
            'properties', COALESCE(pp.properties, JSON_ARRAY()),
            'children', pv.children
        ) ORDER BY p.id  -- Сортировка товаров по ID (можно изменить на популярность или имя)
    ) AS products
FROM product p
LEFT JOIN product_variants pv ON p.id = pv.product_id
LEFT JOIN product_properties pp ON p.id = pp.product_id
LEFT JOIN product_images pi ON p.id = pi.product_id
WHERE p.category_id = (SELECT c.id FROM category c WHERE c.slug = :slug)";
    }

    public function getParam(): array
    {
        return [
            self::CATEGORY_PARAM => $this->slug,
        ];
    }

    public function getType(): array
    {
        return [
            self::CATEGORY_PARAM => 'string',
        ];
    }
}
