<?php

namespace App\Model\Cart;

use Symfony\Component\Validator\Constraints as Assert;

class CartUpdateDto
{
    /**
     * @param CartItemTypeDto[] $cartItems
     */
    public function __construct(

        #[Assert\NotBlank(message: 'Hash корзины обязателен')]
        #[Assert\Length(max: 255)]
        public string $hash,
        /**
         * @var CartItemTypeDto[]
         */
        #[Assert\Valid]
        public readonly array $cartItems = [],

    )
    {
    }
}
