<?php

namespace App\Model\Cart;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CartItemDto
{
    private const METHOD_TYPE = [
        'inc',
        'dec',
        'set',
        'del',
    ];

    public function __construct(
        #[Assert\Uuid(message: 'Невалидный идентификатор.')]
        public Uuid   $productId,

        #[Assert\Positive(message: 'Количество должно быть положительным числом.')]
        public int    $quantity,

        #[Assert\Choice(choices: self::METHOD_TYPE)]
        public string $type,
    )
    {
    }
}
