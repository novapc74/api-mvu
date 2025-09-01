<?php

namespace App\Service\QueryParam;

use Generator;

interface QueryParamInterface
{
    public function getFilters(): Generator;

    public function getOrders(): Generator;
}
