<?php

namespace App\Service\Cart;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Exception\CustomException;
use App\Model\Cart\CartItemDto;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Uid\Uuid;

final readonly class ApiCartService
{
    private const TYPE_INCREMENT = 'inc';
    private const TYPE_DECREMENT = 'dec';
    private const TYPE_DELETE = 'del';
    private const CART_LOCK_PREFIX = 'cart_lock_';
    private const CART_HASH_TTL = 30;

    public function __construct(
        private CartHelper  $cartHelper,
        private LockFactory $lockFactory,
        private EntityManagerInterface $entityManager,
        private CartHash               $hasher,
    )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function findOrMakeNewCart(): Response
    {
        $response = new Response();

        $this->cartHelper->setCartToCookie($response);

        return $response;
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    public function updateCart(CartItemDto $dto): array
    {
        return $this->withCartLock($dto->cartHash, function () use ($dto) {

            if (!$cart = $this->findCartByHash($dto->cartHash)) {
                #TODO Не выбрасывать исключение, а создавать корзину, и доавлять в нее товар.
                throw new CustomException('Корзина не найдена.', 404);
            }

            $cartId = $cart->getId()->toRfc4122();
            $productId = $dto->productId->toRfc4122();
            $cartItem = $this->findCartItemByCartIdAndProductId($cartId, $productId);

            /** нет $cartItem + хотим уменьшить или удалить */
            if (!$cartItem && in_array($dto->type, [self::TYPE_DECREMENT, self::TYPE_DELETE], true)) {
                throw new CustomException('Операция не применима для товара не из корзины.', 422);
            }

            if (!$cartItem) {

                if (!$product = $this->entityManager->getRepository(Product::class)->find($productId)) {
                    throw new CustomException('Продукт не найден.', 422);
                }

                $cartItem = (new CartItem())
                    ->setProduct($product)
                    ->setCart($cart);

                $this->entityManager->persist($cartItem);
            }

            $this->resolveCartItem($cartItem, $dto);

            if (0 === $cart->getCartItems()->count()) {
                $this->entityManager->remove($cart);
                $this->entityManager->flush();

                throw new CustomException('Удалена пустая корзина.', 404);
            }

            $this->entityManager->flush();

            return $this->toArray($cart);
        });
    }

    /**
     * @throws CustomException
     */
    private function withCartLock(string $cartHash, callable $callback): mixed
    {
        $lock = $this->lockFactory->createLock(self::CART_LOCK_PREFIX . $cartHash, self::CART_HASH_TTL);

        $maxAttempts = 5;
        $attempt = 0;
        $waitMicroseconds = 100_000; // 100 ms

        do {
            $attempt++;
            if ($lock->acquire()) {
                try {
                    $result = $callback();
                } finally {
                    $lock->release();
                }
                return $result;
            }

            usleep($waitMicroseconds);

        } while ($attempt < $maxAttempts);

        $errorMessage = sprintf('Не удалось получить блокировку корзины после %d попыток', $maxAttempts);
        throw new CustomException($errorMessage, Response::HTTP_CONFLICT);
    }

    private function toArray(Cart $cart): array
    {
        return [
            'cart' => [
                'cart_hash' => $this->getCartHash($cart),
                'cart_items' => $cart->getCartItems()->map(fn(CartItem $cartItem) => [
                    'product_id' => $cartItem->getProduct()->getId()->toRfc4122(),
                    'quantity' => $cartItem->getQuantity()
                ])->toArray(),
            ]
        ];
    }

    private function getCartHash(Cart $cart): string
    {
        return $this->hasher->encodeHash(
            $cart->getId()->toRfc4122()
        );
    }

    /**
     * @param CartItem $cartItem
     * @param CartItemDto $dto
     */
    private function resolveCartItem(CartItem $cartItem, CartItemDto $dto): void
    {
        match ($dto->type) {
            self::TYPE_INCREMENT => self::incrementCartItem($cartItem),
            self::TYPE_DECREMENT => self::decrementCartItem($cartItem),
            self::TYPE_DELETE => self::deleteCartItem($cartItem),
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

            if (!$cart = $this->findCartByHash($hash)) {
                return;
            }

            $this->entityManager->remove($cart);
            $this->entityManager->flush();
        });
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    private function findCartByHash(string $hash): Cart
    {
        $cartId = $this->hasher->decodeHash($hash);

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

}
