<?php

namespace App\Controller;

use Exception;
use App\Model\Cart\CartItemDto;
use App\Service\Cart\CartService;
use App\Exception\CustomException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Service\ApiResponse\ApiResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
final class CartController extends AbstractController
{
    public function __construct(
        private readonly CartService           $cartService,
    )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/cart', methods: ['GET'])]
    public function getCartFromSession(): JsonResponse
    {
        try {
            return new JsonResponse(
                ApiResponseFactory::successResponse($this->cartService->getCartFromSession())
            );
        } catch (CustomException $exception) {
            return new JsonResponse(
                ApiResponseFactory::errorResponse($exception),
                $exception->getCode() ?: Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @throws ORMException
     */
    #[Route('/cart/{hash}', methods: ['GET'])]
    public function getCart(string $hash): JsonResponse
    {
        try {
            return new JsonResponse(
                ApiResponseFactory::successResponse($this->cartService->getCart($hash))
            );
        } catch (Exception $error) {
            return new JsonResponse(
                ApiResponseFactory::errorResponse($error),
                $error->getCode() ?: Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/cart', methods: ['POST'])]
    public function createCart(): JsonResponse
    {
        try {
            return new JsonResponse(
                ApiResponseFactory::successResponse($this->cartService->createCart())
            );
        } catch (Exception $exception) {
            return new JsonResponse(
                ApiResponseFactory::errorResponse($exception),
                $exception->getCode() ?: Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/cart', methods: ['PUT'])]
    public function updateCart(#[MapRequestPayload] CartItemDto $cartItemDto): JsonResponse
    {
        try {
            return new JsonResponse(
                ApiResponseFactory::successResponse(
                    $this->cartService->updateCart($cartItemDto)
                )
            );
        } catch (CustomException $exception) {
            return new JsonResponse(
                ApiResponseFactory::errorResponse($exception),
                $exception->getCode() ?: Response::HTTP_BAD_REQUEST
            );
        }
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

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (CustomException $exception) {
            return new JsonResponse(
                ApiResponseFactory::errorResponse($exception),
                $exception->getCode() ?: Response::HTTP_BAD_REQUEST);
        }
    }
}
