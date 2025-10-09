<?php

namespace App\Service\Api\Product\Dto;

use InvalidArgumentException;
use App\Service\Api\Product\Interface\SqlInterface;

final readonly class ProductPageDto implements SqlInterface
{
    private const PRODUCT_PARAM= 'slug';
    public function __construct(private string $slug)
    {
    }

    public static function init(mixed $data): SqlInterface
    {
        if (!is_string($data)) {
            throw new InvalidArgumentException('Data must be string');
        }

        return new self($data);
    }

    public function getSql(): string
    {
        return "WITH variant_stocks AS (
    SELECT
        pv.id AS variant_id,
        JSON_ARRAYAGG(
            JSON_OBJECT(
                'price', CONCAT(st.price, ' ', 'руб.'),
                'amount', st.amount,
                'warehouse', JSON_OBJECT(
                                'name', w.name,
                                'address', w.address
                                )
            )
        ) AS stocks
    FROM product_variant pv
    LEFT JOIN stock st ON pv.id = st.product_variant_id
    INNER JOIN warehouse w ON st.warehouse_id = w.id
    GROUP BY pv.id
)
SELECT
    LOWER(
        CONCAT_WS('-', SUBSTR(HEX(p.id), 1, 8), SUBSTR(HEX(p.id), 9, 4), SUBSTR(HEX(p.id), 13, 4), SUBSTR(HEX(p.id), 17, 4), SUBSTR(HEX(p.id), 21))
    ) AS id,
    p.name,
    p.slug,
    COUNT(pv.id) AS product_variant_count,
    JSON_ARRAYAGG(
         DISTINCT JSON_OBJECT(
            'path', 'https://localhost/upload/images/default.wav',
            'is_main', TRUE
        )
    ) AS images,
    JSON_ARRAYAGG(
        DISTINCT JSON_OBJECT(
            pr.name, pp.value,
            'scale', pp.scale
        ) ORDER BY pp.scale ASC
    ) AS properties,
    JSON_ARRAYAGG(
        DISTINCT JSON_OBJECT(
            'variant_id', pv.id,
            'popularity_index', pv.popularity_index,
            'size', s.size,
            'gender', g.gender,
            'color', JSON_OBJECT(
                'color', c.name,
                'hex_code', c.hex_code
            ),
            'stock', COALESCE(vs.stocks, JSON_ARRAY())
        ) ORDER BY s.id, g.id, c.id -- сортируем по размерам, полу, цветам ...
    ) AS children
FROM product p
    LEFT JOIN product_property pp ON p.id = pp.product_id
    INNER JOIN property pr ON pr.id = pp.property_id
    LEFT JOIN product_variant pv ON p.id = pv.product_id
    INNER JOIN size s ON pv.size_id = s.id
    INNER JOIN gender g ON pv.gender_id = g.id
    INNER JOIN color c ON pv.color_id = c.id
    LEFT JOIN variant_stocks vs ON pv.id = vs.variant_id
WHERE p.slug = :slug;";
    }

    public function getParam(): array
    {
        return [
            self::PRODUCT_PARAM => $this->slug,
        ];
    }

    public function getType(): array
    {
        return [
            self::PRODUCT_PARAM => 'string',
        ];
    }
}
