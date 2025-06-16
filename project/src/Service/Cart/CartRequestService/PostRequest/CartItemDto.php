<?php

namespace App\Service\Cart\CartRequestService\PostRequest;

readonly class CartItemDto
{
    public function __construct(private string $productId, private int|float $quantity)
    {
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getQuantity(): int|float
    {
        return $this->quantity;
    }
}
