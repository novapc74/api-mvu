<?php

namespace App\Controller;

use App\Entity\Product;
use App\Model\Product\ProductIdDto;
use App\Service\Product\ProductService;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
final class ProductController extends AbstractController
{
    #[Route('/products', methods: ['POST'])]
    public function index(ProductService $service, #[MapRequestPayload(type: ProductIdDto::class)] array $dto): JsonResponse
    {
        return new JsonResponse($service->getProducts($dto));
    }

    #[Route('/product/{slug}', methods: ['GET'])]
    public function show(ProductService $service, Product $product): JsonResponse
    {
        return new JsonResponse($service->getProductPage($product));
    }
}
