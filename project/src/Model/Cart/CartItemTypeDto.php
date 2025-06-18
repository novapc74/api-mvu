<?php

namespace App\Model\Cart;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CartItemTypeDto
{
    private const AVAILABLE_TYPE = [
        'increment', 'decrement', 'override', 'delete'
    ];

    public function __construct(
        #[Assert\Uuid(message: 'Невалидный идентификатор')]
        public Uuid   $id,

        public float  $quantity,

        #[Assert\Choice(choices: self::AVAILABLE_TYPE, message: 'Невалидный тип операции.')]
        public string $type,
    )
    {
    }
}
