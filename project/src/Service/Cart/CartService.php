<?php

namespace App\Service\Cart;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\CartItem;
use Symfony\Component\Uid\Uuid;
use App\Trait\CryptographyTrait;
use App\Exception\CustomException;
use App\Model\Cart\CartItemTypeDto;
use App\Repository\CartItemRepository;
use Symfony\Component\Lock\LockFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Response;

readonly class CartService
{
    use CryptographyTrait;

    private const CART_LOCK_PREFIX = 'cart_lock_';
    private const CART_HASH_TTL = 600;

    public function __construct(private EntityManagerInterface $entityManager,
                                private LockFactory            $lockFactory,
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
        $cart = $this->findCartByHash($hash);

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
    public function updateCart(CartItemTypeDto $cartItemDto): array
    {
        return $this->withCartLock($cartItemDto->cartHash, function () use ($cartItemDto) {

            $cart = $this->findCartByHash($cartItemDto->cartHash);

            $cartId = $cart->getId()->toRfc4122();
            $productId = $cartItemDto->productId->toRfc4122();
            $cartItem = $this->findCartItemByCartIdAndProductId($cartId, $productId);

            /** нет $cartItem + хотим уменьшить или удалить */
            if (!$cartItem && in_array($cartItemDto->type, ['dec', 'del'])) {
                return $this->toArray($cart);
            }

            if (!$cartItem) {

                if (!$product = $this->entityManager->getRepository(Product::class)->find($productId)) {
                    return $this->toArray($cart);
                }

                $cartItem = (new CartItem())
                    ->setProduct($product)
                    ->setCart($cart);

                $this->entityManager->persist($cartItem);
            }

            $this->resolveCartItem($cartItem, $cartItemDto);

            $this->entityManager->flush();

            return $this->toArray($cart);
        });
    }

    private function resolveCartItem(CartItem $cartItem, CartItemTypeDto $dto): void
    {
        match ($dto->type) {
            'inc' => self::incrementCartItem($cartItem),
            'dec' => self::decrementCartItem($cartItem),
            'del' => self::deleteCartItem($cartItem),
            default => self::overrideCartItem($cartItem, $dto->quantity)
        };
    }

    private function incrementCartItem(CartItem $cartItem): void
    {
        $cartItem->setQuantity($cartItem->getQuantity() + 1);
    }

    private function decrementCartItem(CartItem $cartItem): void
    {
        $quantity = $cartItem->getQuantity();
        $result = $quantity - 1;

        $result > 0
            ? $cartItem->setQuantity($result)
            : $this->deleteCartItem($cartItem);
    }

    private function deleteCartItem(CartItem $cartItem): void
    {
        $this->entityManager->remove($cartItem);
    }

    private function overrideCartItem(CartItem $cartItem, int|float $quantity): void
    {
        $quantity > 0 ?
            $cartItem->setQuantity($quantity)
            : $this->deleteCartItem($cartItem);
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    public function deleteCart(string $hash): void
    {
        $this->withCartLock($hash, function () use ($hash) {
            $cart = $this->findCartByHash($hash);

            $this->entityManager->remove($cart);
            $this->entityManager->flush();
        });
    }

    private function toArray(Cart $cart): array
    {
        $cartId = $cart->getId()->toRfc4122();
        $cartHash = self::encodeHash($this->cartSalt, $cartId);

        return [
            'success' => true,
            'cart' => [
                'cart_hash' => $cartHash,
                'cart_items' => $cart->getCartItems()->map(fn(CartItem $cartItem) => [
                    'productId' => $cartItem->getProduct()->getId()->toRfc4122(),
                    'quantity' => $cartItem->getQuantity()
                ])->toArray(),
            ]
        ];
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    private function findCartByHash(string $hash): Cart
    {
        $cartId = self::decodeHash($this->cartSalt, $hash);

        if (!Uuid::isValid($cartId)) {
            throw new CustomException(sprintf('Невалидный hash: "%s".', $hash), 422);
        }

        if (!$cart = $this->entityManager->find(Cart::class, $cartId)) {
            throw new CustomException('Корзина не найдена.', 404);
        }

        return $cart;
    }

    private function findCartItemByCartIdAndProductId(string $cartId, string $productId): ?CartItem
    {
        /**@var CartItemRepository $repository */
        $repository = $this->entityManager->getRepository(CartItem::class);

        return $repository->findCartItemByProduct($cartId, $productId);
    }

    /**
     * @throws CustomException
     */
    private function withCartLock(string $cartHash, callable $callback): mixed
    {
        $lock = $this->lockFactory->createLock(self::CART_LOCK_PREFIX . $cartHash, 30);

        if (!$lock->acquire()) {
            throw new CustomException('Не удалось получить блокировку корзины', Response::HTTP_CONFLICT);
        }

        try {
            return $callback();
        } finally {
            $lock->release();
        }
    }
}
