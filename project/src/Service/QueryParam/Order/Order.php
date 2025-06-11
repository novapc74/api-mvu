<?php

namespace App\Service\QueryParam\Sort;

readonly class Order
{
    public function __construct(private string $tableName, private string $field, private string $type)
    {
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
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
