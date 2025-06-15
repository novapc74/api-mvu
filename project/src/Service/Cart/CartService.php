<?php

namespace App\Service\Cart;

use App\Entity\Cart;
use Symfony\Component\Uid\Uuid;
use App\Exception\CustomException;
use App\Service\Crypto\Cryptography;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

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

    public function createCart(): array
    {
        $cart = new Cart();

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
