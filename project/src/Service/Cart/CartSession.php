<?php

namespace App\Service\Cart;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartSession
{
    private const CART_SESSION_KEY = 'csk';

    private SessionInterface $session;

    public function __construct(
        RequestStack $requestStack,
    )
    {
        $this->session = $requestStack->getSession();
    }

    public function getCartHashFromSession(): ?string
    {
        return $this->session->get(self::CART_SESSION_KEY);
    }

    public function setCartHashToSession(string $hash): void
    {
        $this->session->set(self::CART_SESSION_KEY, $hash);
    }
}
