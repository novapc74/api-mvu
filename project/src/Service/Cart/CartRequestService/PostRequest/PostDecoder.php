<?php

namespace App\Service\Cart\CartRequestService\PostRequest;

use Generator;
use Symfony\Component\Uid\Uuid;

class PostDecoder
{
    private array $newCartItems = [];

    public function __construct(array $requestData)
    {
        $this->initialize($requestData);
    }

    public static function init(array $requestData): self
    {
        return new self($requestData);
    }

    private function initialize(array $requestData): void
    {

        if (!array_key_exists('cart_items', $requestData)) {
            return;
        }

        if (!is_array($requestData['cart_items'])) {
            return;
        }

        foreach ($requestData['cart_items'] as $value) {
            if (!array_key_exists('product_id', $value) || !array_key_exists('quantity', $value)) {
                continue;
            }

            $productId = $value['product_id'];
            $quantity = $value['quantity'];

            if (!Uuid::isValid($productId) || !is_numeric($quantity)) {
                continue;
            }

            $this->newCartItems[] = new CartItemDto($productId, $quantity);
        }
    }

    public function getCartItemDto(): Generator
    {
        yield from $this->newCartItems;
    }
}
