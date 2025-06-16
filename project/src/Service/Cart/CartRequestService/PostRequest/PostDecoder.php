<?php

namespace App\Service\Cart\CartRequestService\PostRequest;

use Generator;
use Symfony\Component\Uid\Uuid;
use App\Exception\CustomException;

class PostDecoder
{
    private array $newCartItems = [];

    /**
     * @throws CustomException
     */
    public function __construct(array $requestData)
    {
        $this->initialize($requestData);
    }

    /**
     * @throws CustomException
     */
    public static function init(array $requestData): self
    {
        return new self($requestData);
    }

    /**
     * @throws CustomException
     */
    private function initialize(array $requestData): void
    {

        if (!array_key_exists('cart_items', $requestData)) {
            throw new CustomException('Невалидное тело запроса, ключ "cart_items" не найден', 422);
        }

        if (!is_array($requestData['cart_items'])) {
            throw new CustomException('Передайте содержание новой корзины в массивом.', 422);
        }

        foreach ($requestData['cart_items'] as $value) {
            if (!array_key_exists('product_id', $value) && array_key_exists('quantity', $value)) {
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
