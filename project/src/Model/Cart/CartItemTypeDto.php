<?php

namespace App\Model\Cart;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\QuantityTypeConstraint;

#[QuantityTypeConstraint]
readonly class CartItemTypeDto
{
    private const AVAILABLE_TYPE = [
        'inc', 'dec', 'del'
    ];

    public function __construct(
        #[Assert\Uuid(message: 'Невалидный идентификатор')]
        public Uuid   $id,

        public ?float  $quantity,

        #[Assert\Choice(choices: self::AVAILABLE_TYPE, message: 'Невалидный тип операции.')]
        public ?string $type,
    )
    {
    }
}
