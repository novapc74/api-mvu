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
     * @throws CustomException
     * @throws ORMException
     */
    #[Route('/cart', name: 'api_cart', methods: ['GET'])]
    public function show(): Response
    {
        return ApiResponseFactory::responseHelper(
            $this->service->getApiCart()
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/cart', name: 'api_cart_show_or_create', methods: ['POST'])]
    public function findOrCreateCart(): Response
    {
        return ApiResponseFactory::responseHelper(
            $this->service->findOrCreateCart()
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws CustomException
     * @throws ORMException
     */
    #[Route('/cart/update', name: 'api_cart_update', methods: ['POST'])]
    public function addToCart(#[MapRequestPayload] CartItemDto $cartItemDto): Response
    {
        return ApiResponseFactory::responseHelper([
            'quantity' => $this->service->updateCart($cartItemDto)
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/cart/dropdown', name: 'api_cart_dropdown')]
    public function dropdown(): Response
    {
        return $this->render('pages/cart/_embed/dropdown_items.html.twig', [
            'items' => $this->service->getItems()
        ]);
    }
}
