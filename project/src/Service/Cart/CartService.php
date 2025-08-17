<?php

namespace App\Service\Cart;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Exception\CustomException;
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
        if(!$cart = $this->cartHelper->getCart()) {
            return [];
        }

        return $this->toArray($cart);
    }


    private function toArray(Cart $cart): array
    {
        return [
            'cart' => [
                'id' => $cart->getId()->toRfc4122(),
                'cart_items' => $this->getCartItems($cart)
            ]
        ];
    }

    private function getCartItems(Cart $cart): array
    {
        $cartItems = [];
        foreach ($cart->getCartItems() as $cartItem) {
            $cartItems[] = [
                'product_id' => $cartItem->getProduct()->getId()->toRfc4122(),
                'quantity' => $cartItem->getQuantity()
            ];
        }

        return $cartItems;
    }


}
