<?php

namespace App\Controller;

use Exception;
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
     * @throws ORMException
     */
    #[Route('/cart', name: 'cart_from_session', methods: ['GET'])]
    public function getCartFromSession(): JsonResponse
    {
        try {
            $responseData = $this->cartService->getCartFromSession();
        } catch (Exception $exception) {
            $responseData = $exception;
        }

        return ApiResponseFactory::responseHelper($responseData);
    }

    /**
     * @throws ORMException
     */
    #[Route('/cart/{hash}', methods: ['GET'])]
    public function getCart(string $hash): JsonResponse
    {
        try {
            $responseData = $this->cartService->getCart($hash);
        } catch (Exception $error) {
            $responseData = $error;
        }

        return ApiResponseFactory::responseHelper($responseData);
    }

    #[Route('/cart', methods: ['POST'])]
    public function createCart(): JsonResponse
    {
        try {
            $responseData = $this->cartService->createCart();
        } catch (Exception $exception) {
            $responseData = $exception;
        }

        return ApiResponseFactory::responseHelper($responseData);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/cart', methods: ['PUT'])]
    public function updateCart(#[MapRequestPayload] CartItemDto $cartItemDto): JsonResponse
    {
        try {
            $responseData = $this->cartService->updateCart($cartItemDto);
        } catch (Exception $exception) {
            $responseData = $exception;
        }

        return ApiResponseFactory::responseHelper($responseData);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/cart/{hash}', methods: ['DELETE'])]
    public function deleteCart(string $hash): JsonResponse
    {
        try {
            $this->cartService->deleteCart($hash);
            $responseData = [];
        } catch (Exception $exception) {
            $responseData = $exception;
        }

        return ApiResponseFactory::responseHelper($responseData);
    }
}
