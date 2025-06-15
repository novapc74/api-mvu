<?php

namespace App\Controller;

use App\Exception\CustomException;
use App\Service\Cart\CartService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
final class CartController extends AbstractController
{
    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    #[Route('/cart/{hash}')]
    public function getCart(CartService $service, string $hash): JsonResponse
    {
        $cart = $service->getCart($hash);

        return new JsonResponse($cart);
    }

    #[Route('/cart', methods: ['POST'])]
    public function createCart(CartService $service): JsonResponse
    {
        $cart = $service->createCart();

        return new JsonResponse($cart);
    }

    #[Route('/cart', methods: ['PATCH'])]
    public function updateCart(CartService $service): JsonResponse
    {
        $cart = $service->updateCart();

        return new JsonResponse($cart);
    }

    #[Route('/cart/{hash}', methods: ['DELETE'])]
    public function deleteCart(CartService $service, string $hash): JsonResponse
    {
        $service->deleteCart($hash);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
