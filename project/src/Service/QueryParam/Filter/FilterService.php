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
        foreach ($filterDataFromRequest as $tableName => $filterData) {

            if (!is_array($filterData)) {
                continue;
            }

            foreach ($filterData as $field => $childFilterData) {

                if (!is_array($childFilterData)) {
                    continue;
                }

                if (!$type = self::getFilterType($childFilterData)) {
                    continue;
                }

                if (!$filterValue = $this->getFilterValue($childFilterData)) {
                    continue;
                }

                $this->addFilter($tableName, $field, $type, $filterValue);
            }
        }
    }

    private static function getFilterType(array $filterFromRequest): ?string
    {
        $filterType = array_key_first($filterFromRequest);

        if (is_string($filterType)) {
            $filterType = strtolower($filterType);
        }

        return in_array($filterType, self::AVAILABLE_TYPES) ? $filterType : null;
    }

    private static function getFilterValue(array $filterFromRequest): mixed
    {
        return $filterFromRequest[self::getFilterType($filterFromRequest)] ?? null;
    }

    private function addFilter(string $tableName, string $field, string $type, mixed $value): void
    {
        $this->filters[] = new Filter($tableName, $field, $type, $value);
    }
}
