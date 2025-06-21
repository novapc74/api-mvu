<?php

namespace App\Model\Cart;

use Symfony\Component\Uid\Uuid;
use App\Validator\QuantityTypeConstraint;
use Symfony\Component\Validator\Constraints as Assert;

#[QuantityTypeConstraint]
readonly class CartItemTypeDto
{
    private const AVAILABLE_TYPE = [
        'inc', 'dec', 'del'
    ];

    public function __construct(
        #[Assert\NotBlank(message: 'Hash корзины обязателен')]
        #[Assert\Length(max: 255)]
        public string  $cartHash,

        #[Assert\Uuid(message: 'Невалидный идентификатор')]
        public Uuid    $productId,

        public ?float  $quantity,

        #[Assert\Choice(choices: self::AVAILABLE_TYPE, message: 'Невалидный тип операции.')]
        public ?string $type,
    )
    {
    }
}
