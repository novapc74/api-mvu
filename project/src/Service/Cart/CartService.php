<?php

namespace App\Service\Cart;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\CartItem;
use Symfony\Component\Uid\Uuid;
use App\Model\Cart\CartUpdateDto;
use App\Exception\CustomException;
use App\Model\Cart\CartItemTypeDto;
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

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    public function updateCart(CartUpdateDto $cartDto): array
    {
        $cart = self::findCardByHash($cartDto->hash);

        foreach ($cartDto->cartItems as $cartItemDto) {

            [$productId, $quantity, $operationType] = self::getCartItemData($cartItemDto);

            if (!$cartItem = $this->entityManager->getRepository(CartItem::class)->findCartItemByProduct($cart->getId(), $productId)) {

                if (!$product = $this->entityManager->find(Product::class, $productId)) {
                    continue;
                }

                $cartItem = (new CartItem())
                    ->setCart($cart)
                    ->setProduct($product);
            }

            $this->entityManager->persist($cartItem);
            $this->resolveCartItem($cartItem, $quantity, $operationType);
        }

//        $this->entityManager->flush();

        return $this->toArray($cart);
    }

    private function resolveCartItem(CartItem $cartItem, float $quantity, string $operationType): void
    {
        match ($operationType) {
            'inc' => self::incrementCartItem($cartItem),
            'dec' => self::decrementCartItem($cartItem),
            'del' => self::deleteCartItem($cartItem),
            default => self::overrideCartItem($cartItem, $quantity)
        };
    }

    private function incrementCartItem(CartItem $cartItem): void
    {
        #TODO увеличиваем / создаем на единицу...
    }

    private function decrementCartItem(CartItem $cartItem): void
    {
        #TODO уменьшаем / удаляем на единицу...

    }

    private function deleteCartItem(CartItem $cartItem): void
    {
        #TODO удаляем ...

    }

    private function overrideCartItem(CartItem $cartItem, int|float $quantity): void
    {
        #TODO перезаписываем / создаем ...
    }

    private static function getCartItemData(CartItemTypeDto $dto): array
    {
        return [
            $dto->id,
            $dto->quantity,
            $dto->type,
        ];
    }

    public function deleteCart(string $hash): void
    {

    }

    private function toArray(Cart $cart): array
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
