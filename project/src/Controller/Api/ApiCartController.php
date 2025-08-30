<?php

namespace App\Controller\Api;

use App\Model\Cart\CartItemDto;
use App\Exception\CustomException;
use App\Service\Cart\ApiCartService;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Service\ApiResponse\ApiResponseFactory;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
final class ApiCartController extends AbstractController
{
    public function __construct(private readonly ApiCartService $service)
    {
    }

    #[Route('/cart', name: 'api_cart', methods: ['POST'])]
    public function findOrCreateCart(): Response
    {
        return ApiResponseFactory::responseHelper(
            $this->service->findOrCreateCart()
        );
    }

    /**
     * @throws CustomException
     */
    #[Route('/cart/update', name: 'api_cart_update', methods: ['POST'])]
    public function addToCart(#[MapRequestPayload] CartItemDto $cartItemDto): Response
    {
        return ApiResponseFactory::responseHelper([
            'quantity' => $this->service->updateCart($cartItemDto)
        ]);
    }
}
