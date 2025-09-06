<?php

namespace App\Service\Cart;

use App\Entity\Cart;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

readonly class CartService
{
    public function __construct(
        private CartHelper $cartHelper,
    )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function getCart(): array
    {
        if (!$cart = $this->cartHelper->getCart()) {
            return [];
        }

        return self::toArray($cart);
    }

    public static function toArray(Cart $cart): array
    {
        return [
            'cart' => [
                'items_count' => $cart->getCartItems()?->count() ?? 0,
                'cart_items' => self::getCartItems($cart)
            ]
        ];
    }

    private static function getCartItems(Cart $cart): array
    {
        $cartItems = [];
        foreach ($cart->getCartItems() as $cartItem) {
            $cartItems[] = [
                'id' => $cartItem->getProduct()->getId()->toRfc4122(),
                'name' => $cartItem->getProduct()->getName(),
                'quantity' => $cartItem->getQuantity()
            ];
        }

        return array_values($cartItems);
    }
}
