<?php

namespace App\Controller;

use App\Model\Cart\CartItemDto;
use App\Service\Cart\CartService;
use App\Exception\CustomException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ApiResponse\ApiResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
final class CartController extends AbstractController
{
    public function __construct(
        private readonly CartService $cartService,
    )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    #[Route('/cart', name: 'cart_from_session', methods: ['GET'])]
    public function getCartFromSession(): JsonResponse
    {
        return ApiResponseFactory::responseHelper(
            $this->cartService->getCartFromSession()
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    #[Route('/cart/{hash}', name: 'cart_by_hash', methods: ['GET'])]
    public function getCart(string $hash): JsonResponse
    {
        return ApiResponseFactory::responseHelper(
            $this->cartService->getCart($hash)
        );
    }

    #[Route('/cart', name: 'cart_new', methods: ['POST'])]
    public function createCart(): JsonResponse
    {
        return ApiResponseFactory::responseHelper(
            $this->cartService->createCart()
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    #[Route('/cart', name: 'cart_update', methods: ['PUT'])]
    public function updateCart(#[MapRequestPayload] CartItemDto $cartItemDto): JsonResponse
    {
        return ApiResponseFactory::responseHelper(
            $this->cartService->updateCart($cartItemDto)
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    #[Route('/cart/{hash}', name: 'cart_delete', methods: ['DELETE'])]
    public function deleteCart(string $hash): JsonResponse
    {
        $this->cartService->deleteCart($hash);

        return ApiResponseFactory::responseHelper([]);
    }
}
