<?php

namespace App\Service\QueryParam\Filter;

use Generator;

class FilterService
{
    private array $filters = [];

    private const AVAILABLE_TYPES = [
        'eq', 'in', 'range', 'lt', 'gt', 'lte', 'gte'
    ];

    public function __construct(array $filterDataFromRequest)
    {
        $this->initializeFilters($filterDataFromRequest);
    }

    public static function init(array $filterDataFromRequest): self
    {
        return new self($filterDataFromRequest);
    }

    public function getFilters(): Generator
    {
        yield from $this->filters;
    }

    private function initializeFilters(array $filterDataFromRequest): void
    {
        foreach ($filterDataFromRequest as $filterTableName => $filterData) {

            if (!is_array($filterData)) {
                continue;
            }

            $tableName = $filterTableName;

            foreach ($filterData as $field => $item) {

                if (!is_array($item)) {
                    continue;
                }

                if (!$type = self::getType($item)) {
                    continue;
                }

                if (!$value = $this->getValue($item)) {
                    continue;
                }

                $this->addFilter($tableName, $field, $type, $value);
            }
        }
    }

    private static function getType(array $filterFromRequest): ?string
    {
        $type = array_key_first($filterFromRequest);

        return in_array($type, self::AVAILABLE_TYPES) ? $type : null;
    }

    private static function getValue(array $filterFromRequest): mixed
    {
        return array_values($filterFromRequest)[0] ?? null;
    }

    private function addFilter(string $tableName, string $field, string $type, mixed $value): void
    {
        $this->filters[] = new Filter($tableName, $field, $type, $value);
    }
}
