<?php

namespace App\Model\Product;

use Symfony\Component\Validator\Constraints as Assert;

class ProductSearchDto
{
    public function __construct(
        #[Assert\Type('string', 'Ожидаемый тип - строка.')]
        public ?string $search = null,
    )
    {
    }

}
