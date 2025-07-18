<?php

namespace App\Controller;

use App\Service\Cart\CartService;
use App\Exception\CustomException;
use App\Model\Cart\CartItemTypeDto;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
final class CartController extends AbstractController
{
    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    #[Route('/cart/{hash}', methods: ['GET'])]
    public function getCart(CartService $service, string $hash): JsonResponse
    {
        return new JsonResponse($service->getCart($hash));
    }

    #[Route('/cart', methods: ['POST'])]
    public function createCart(CartService $service): JsonResponse
    {
        return new JsonResponse($service->createCart(), Response::HTTP_CREATED);
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    #[Route('/cart', methods: ['PUT'])]
    public function updateCart(#[MapRequestPayload] CartItemTypeDto $cartItemDto, CartService $service): JsonResponse
    {
        return new JsonResponse($service->updateCart($cartItemDto));
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    #[Route('/cart/{hash}', methods: ['DELETE'])]
    public function deleteCart(CartService $service, string $hash): JsonResponse
    {
        $service->deleteCart($hash);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
