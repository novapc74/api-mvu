<?php

namespace App\Service\Product\Adapter;

interface SqlDoctrineInterface
{
    public function getEntityFqcn(): string;

    public function getSql(): string;

    public function getTypes(): array;

    public function getParams(): array;
}
