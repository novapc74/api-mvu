<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\Product\ProductService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{
    #[Route('/product/{slug}', methods: ['GET'])]
    public function show(ProductService $service, Product $product): JsonResponse
    {
        return new JsonResponse($service->getProductPage($product));
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/product', name: 'app_catalog', methods: ['GET'])]
    public function index(ProductService $service): Response
    {

        return $this->render('pages/catalog/catalog.html.twig', [
            'data' => $service->getProducts()
        ]);
    }
}
