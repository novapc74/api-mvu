<?php

namespace App\Controller\Api;

use App\Model\Cart\CartItemDto;
use App\Exception\CustomException;
use App\Service\Cart\ApiCartService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Service\ApiResponse\ApiResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
final class ApiCartController extends AbstractController
{
    public function __construct(private readonly ApiCartService $service)
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/cart', name: 'api_cart', methods: ['POST'])]
    public function index(): Response
    {
        return $this->service->findOrMakeNewCart();
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    #[Route('/cart', name: 'api_cart_update', methods: ['PUT'])]
    public function updateCart(#[MapRequestPayload] CartItemDto $cartItemDto): JsonResponse
    {
        return ApiResponseFactory::responseHelper(
            $this->service->updateCart($cartItemDto)
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    #[Route('/cart/{hash}', name: 'api_cart_delete', methods: ['DELETE'])]
    public function deleteCart(string $hash): JsonResponse
    {
        $this->service->deleteCart($hash);

        return ApiResponseFactory::responseHelper([]);
    }
}
