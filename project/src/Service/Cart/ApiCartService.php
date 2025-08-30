<?php

namespace App\Service\Cart;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\CartItem;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use App\Model\Cart\CartItemDto;
use App\Exception\CustomException;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Symfony\Component\HttpFoundation\Response;

#[WithMonologChannel('cart_service_logger')]
final readonly class ApiCartService
{
    private const CART_NOT_FOUND = 'cart_not_found';
    private const PRODUCT_NOT_FOUND = 'product_not_found';
    private const TYPE_INCREMENT = 'inc';
    private const TYPE_SET = 'set';
    private const TYPE_DECREMENT = 'dec';
    private const TYPE_DELETE = 'del';

    public function __construct(
        private CartHelper             $cartHelper,
        private EntityManagerInterface $entityManager,
        private LoggerInterface        $logger,
    )
    {
    }

    /**
     * @throws CustomException
     */
    public function updateCart(CartItemDto $dto): int
    {
        $this->logger->info('Updated cart.', [
            'productId' => $dto->productId,
            'quantity' => $dto->quantity
        ]);

        $product = $this->getProduct($dto->productId);

        $cart = $this->getCart();

        $cartItem = $this->getCartItem($cart, $product);

        $newQuantity = $this->resolveCartItem($cartItem, $dto);

        $this->entityManager->flush();

        return $newQuantity;
    }

    /**
     * @throws CustomException
     */
    private function getProduct(Uuid $uuid): Product
    {
        if (!$product = $this->entityManager->getRepository(Product::class)->findOneBy([
            'id' => $uuid->toRfc4122()
        ])) {
            $message = sprintf('Товар с id: "%s" не найден.', $uuid->toRfc4122());
            $this->logger->error($message);

            throw new CustomException($message, self::PRODUCT_NOT_FOUND);
        }

        return $product;
    }

    /**
     * @throws CustomException
     */
    private function getCart(): Cart
    {
        if (!$cart = $this->cartHelper->getCart()) {
            $message = 'Корзина не найдена.';
            $this->logger->error($message);

            throw new CustomException($message, self::CART_NOT_FOUND);
        }

        return $cart;
    }

    private function getCartItem(Cart $cart, Product $product): CartItem
    {
        /**@var CartItemRepository $cartItemRepository */
        $cartItemRepository = $this->entityManager->getRepository(CartItem::class);

        if ($cartItem = $cartItemRepository->findCartItemByProduct($cart, $product)) {
            return $cartItem;
        }

        $cartItem = (new CartItem())
            ->setProduct($product)
            ->setCart($cart);

        $this->entityManager->persist($cartItem);

        return $cartItem;
    }

    public function findOrCreateCart(): Response
    {
        $response = new Response();

        $this->cartHelper->setCartToCookie($response);

        return $response;
    }

    private function resolveCartItem(CartItem $cartItem, CartItemDto $dto): int
    {
        return match ($dto->type) {
            self::TYPE_INCREMENT => self::incrementCartItem($cartItem),
            self::TYPE_DECREMENT => self::decrementCartItem($cartItem),
            self::TYPE_DELETE => self::deleteCartItem($cartItem),
            self::TYPE_SET => self::overrideCartItem($cartItem, $dto->quantity)
        };
    }

    private function incrementCartItem(CartItem $cartItem): int
    {
        $quantity = $cartItem->getQuantity() + 1;
        $cartItem->setQuantity($quantity);

        return $quantity;
    }

    private function decrementCartItem(CartItem $cartItem): int
    {
        if (0 < $quantity = max($cartItem->getQuantity() - 1, 0)) {
            $cartItem->setQuantity($quantity);

            return $quantity;
        }

        $cart = $cartItem->getCart();
        if ($cart->getCartItems()->count() === 1 && $cart->getCartItems()->current() === $cartItem) {
            $this->entityManager->remove($cart);

            return $quantity;
        }

        $this->entityManager->remove($cartItem);

        return $quantity;
    }

    private function deleteCartItem(CartItem $cartItem): int
    {
        $this->entityManager->remove($cartItem);

        return 0;
    }

    private function overrideCartItem(CartItem $cartItem, int|float $quantity): int
    {
        $quantity > 0 ?
            $cartItem->setQuantity($quantity)
            : $this->deleteCartItem($cartItem);

        return $quantity;
    }
}
