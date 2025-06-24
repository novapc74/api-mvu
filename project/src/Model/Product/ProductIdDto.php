<?php

namespace App\Model\Product;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ProductIdDto
{
    public function __construct(
        #[Assert\Uuid(message: 'Невалидный идентификатор')]
        public Uuid $id,
    )
    {
    }

}
