<?php

namespace App\Service\Cart;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Symfony\Component\Uid\Uuid;
use App\Exception\CustomException;
use App\Service\Crypto\Cryptography;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use App\Service\Cart\CartRequestService\PostRequest\CartItemDto;
use App\Service\Cart\CartRequestService\PostRequest\PostDecoder;

readonly class CartService
{
    public function __construct(private EntityManagerInterface $entityManager,
                                private string                 $cartSalt)
    {
    }


    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    public function getCart(string $hash): array
    {
        $cart = $this->findCardByHash($hash);

        return $this->toArray($cart);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function createCart(PostDecoder $postDecoder): array
    {
        $cart = new Cart();

        /**@var CartItemDto $cartItemDto */
        foreach ($postDecoder->getCartItemDto() as $cartItemDto) {
            $productId = $cartItemDto->getProductId();
            $quantity = $cartItemDto->getQuantity();
            $product = $this->entityManager->find(Product::class, $productId);

            $cartItem = (new CartItem())
                ->setCart($cart)
                ->setProduct($product)
                ->setQuantity($quantity);

            $this->entityManager->persist($cartItem);
        }

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $this->toArray($cart);
    }

    public function updateCart(): array
    {
        #TODO
        return [];
    }

    public function deleteCart(string $hash): void
    {

    }

    public function toArray(Cart $cart): array
    {
        $cartId = $cart->getId()->toRfc4122();
        $cartHash = $this->encodeHash($cartId);

        return [
            'success' => true,
            'cart' => [
                'cart_hash' => $cartHash,
                'cart_items' => [],
            ]
        ];
    }

    public function encodeHash(string $hash): string
    {
        return Cryptography::encrypt($this->cartSalt, $hash);
    }

    public function decodeHash(string $hash): string
    {
        return Cryptography::decrypt($this->cartSalt, $hash);
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    private function findCardByHash(string $hash): ?Cart
    {
        $cartId = $this->decodeHash($hash);

        if (!Uuid::isValid($cartId)) {
            throw new CustomException(sprintf('Невалидный hash: "%s".', $hash), 422);
        }

        if (!$cart = $this->entityManager->find(Cart::class, $cartId)) {
            throw new CustomException('Корзина с не найдена.', 404);
        }

        return $cart;
    }
}
