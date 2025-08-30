<?php

namespace App\Service\Cart;

use App\Entity\Cart;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;

class CartHelper
{
    private const CART_COOKIE_KEY = 'cck';

    public function __construct(
        private readonly RequestStack           $requestStack,
        private readonly CartHash               $hasher,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function getCart(): ?Cart
    {
        if(!$cartHash = $this->getCartHashFromCookie()) {
            return null;
        }

        return $this->findCartByHash($cartHash);
    }

    public function setCartToCookie(Response $response): void
    {
        if (!$cartHash = $this->getCartHashFromCookie()) {
            $cart = $this->makeNewCart();
            $cartHash = $this->getCartHash($cart);

            $this->setCartHashToCookie($response, $cartHash);

            $this->setDefaultResponse($response);
            return;
        }

        if (!$this->findCartByHash($cartHash)) {
            $cart = $this->makeNewCart();
            $cartHash = $this->getCartHash($cart);

            $this->setCartHashToCookie($response, $cartHash);
        }

        $this->setDefaultResponse($response);
    }

    private function makeNewCart(): Cart
    {
        $cart = new Cart();
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $cart;
    }

    private function getCartHash(Cart $cart): string
    {
        return $this->hasher->encodeHash(
            $cart->getId()->toRfc4122()
        );
    }

    private function setCartHashToCookie(Response $response, string $cartHash): void
    {
        $response->headers->setCookie(
            new Cookie(self::CART_COOKIE_KEY, $cartHash)
        );

        $this->setDefaultResponse($response);
    }

    private function getCartHashFromCookie(): ?string
    {
        return $this->requestStack
            ->getCurrentRequest()
            ->cookies->get(self::CART_COOKIE_KEY);
    }

    private function findCartByHash(string $hash): ?Cart
    {
        $cartId = $this->hasher->decodeHash($hash);

        if (!Uuid::isValid($cartId)) {
            return null;
        }

        if (!$cart = $this->entityManager->find(Cart::class, $cartId)) {
            return null;
        }

        return $cart;
    }

    private function setDefaultResponse(Response $response): void
    {
        $response->headers->set('Content-Type', 'application/json');

        $response->setContent(json_encode([
            'success' => true,
        ]));
    }
}
