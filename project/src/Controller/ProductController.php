<?php

namespace App\Controller;

use App\Entity\Product;
use App\Model\Product\ProductSearchDto;
use App\Service\Product\ProductService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{
    public function __construct(private readonly ProductService $service)
    {
    }

    #[Route('/product/{slug}', methods: ['GET'])]
    public function show(Product $product): JsonResponse
    {
        return new JsonResponse($this->service->getProductPage($product));
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/product', name: 'app_catalog', methods: ['GET'])]
    public function index(
        #[MapQueryString] ProductSearchDto $dto
    ): Response
    {
        return $this->render('pages/catalog/catalog.html.twig', [
            'data' => $this->service->getProducts($dto)
        ]);
    }
}
