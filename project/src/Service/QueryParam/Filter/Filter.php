<?php

namespace App\Service\QueryParam\Filter;

readonly class Filter
{
    public function __construct(private string $table,
                                private string $field,
                                private string $type,
                                private mixed  $value)
    {
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
