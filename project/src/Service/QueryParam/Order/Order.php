<?php

namespace App\Service\QueryParam\Order;

readonly class Order
{
    public function __construct(private string $table,
                                private string $field,
                                private string $type)
    {
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
