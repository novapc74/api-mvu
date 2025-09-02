<?php

namespace App\Twig\Components;

use App\Service\Cart\ApiCartService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('cart_widget')]
class CartWidgetComponent
{
    use DefaultActionTrait;

    public int $totalItems = 0;

    public function __construct(private readonly ApiCartService $cartService)
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function mount(): void
    {
        $this->totalItems = $this->cartService->getTotalItems();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function refreshCount(): void
    {
        $this->totalItems = $this->cartService->getTotalItems();
    }
}
