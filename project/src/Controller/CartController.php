<?php

namespace App\Controller;

use App\Service\Cart\CartService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ApiResponse\ApiResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CartController extends AbstractController
{
    public function __construct(
        private readonly CartService $service,
    )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function getCartFromSession(): Response
    {
        return $this->render(
            '/pages/cart/cart.html.twig',
            $this->service->getCart()
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/cart/{hash}', name: 'api_cart_by_hash', methods: ['GET'])]
    public function getCart(string $hash): JsonResponse
    {
        return ApiResponseFactory::responseHelper(
            $this->service->getCart()
        );
    }

}
