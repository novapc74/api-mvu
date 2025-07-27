<?php

namespace App\Service\Cart\RequestDto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class CartItemsRequestDto
{
    /**
     * @param CartItemDto[] $cart_items
     */
    public function __construct(
        /**
         * @var CartItemDto[]
         */
        #[Assert\Valid]
        private array $cart_items,
    )
    {
    }

    /**
     * @return CartItemDto[]
     */
    public function getItems(): array
    {
        return $this->cart_items;
    }
}
