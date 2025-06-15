<?php

namespace App\Service\QueryParam\Order;

use Generator;

class OrderService
{
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
        foreach ($orderDataFromRequest as $tableName => $orderData) {

            if (!is_array($orderData)) {
                continue;
            }

            if(!$fieldName = self::getFieldName($orderData)) {
                continue;
            }

            if(!$orderType = self::getOrderType($orderData)) {
                continue;
            }

            $this->addOrder($tableName, $fieldName, $orderType);
        }
    }

    private static function getFieldName(array $orderData): ?string
    {
        return array_key_first($orderData) ?? null;
    }

    private static function getOrderType(array $orderData): ?string
    {
        if($orderType = $orderData[self::getFieldName($orderData)] ?? null) {
            $orderType = strtoupper($orderType);
        }

        return in_array($orderType, self::AVAILABLE_TYPE, true) ? $orderType : null;
    }

    private function addOrder(string $tableName, string $field, string $type): void
    {
        $this->orders[] = new Order($tableName, $field, $type);
    }
}
