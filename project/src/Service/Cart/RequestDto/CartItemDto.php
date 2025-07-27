<?php

namespace App\Service\Cart\RequestDto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class CartItemDto
{
    public function __construct(
        #[Assert\Uuid]
        #[Assert\NotBlank]
        private string $product_id,

        #[Assert\NotBlank]
        private float $quantity,
    )
    {
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getProductId(): string
    {
        return $this->product_id;
    }
}
