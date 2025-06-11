<?php

namespace App\Service\QueryParam\Sort;

use Generator;

class OrderService
{
    // ...test?f[company][id][eq]=10&f[employee][first_name][eq]=John&f[employee][last_name][eq]=Smith&s[company][name]=desc

    private array $orders = [];
    private const AVAILABLE_TYPE = [
        'ASC', 'DESC'
    ];

    public function __construct(array $orderDataFromRequest)
    {
        $this->initializeOrders($orderDataFromRequest);
    }

    public static function init(array $orderDataFromRequest): self
    {
        return new self($orderDataFromRequest);
    }

    public function getOrders(): Generator
    {
        yield from $this->orders;
    }

    private function initializeOrders(array $orderDataFromRequest): void
    {
        foreach ($orderDataFromRequest as $key => $type) {
            [$tableName, $field] = self::decodeRequestData($key);
            $type = is_string($type) ? strtoupper($type) : null;


            if (self::isInvalidData($tableName, $field, $type)) {
                return;
            }

            $this->addOrder($tableName, $field, $type);
        }
    }

    private static function isInvalidData(?string $tableName, ?string $field, ?string $type): bool
    {
        return !$tableName || !$field || !$type || !in_array($type, self::AVAILABLE_TYPE);
    }

    private function addOrder($tableName, $field, $type): void
    {
        $this->orders[] = new Order($tableName, $field, $type);
    }

    private static function decodeRequestData(string $tableField): array
    {
        $data = explode('.', $tableField);

        return [
            $data[0] ?? null,
            $data[1] ?? null,
        ];
    }
}
