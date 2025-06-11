<?php

namespace App\Service\QueryParam\Filter;

readonly class Filter
{
    public function __construct(private string $tableName, private string $field, private string $type, private mixed $value)
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

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

}
