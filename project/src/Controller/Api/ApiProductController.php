<?php

namespace App\Controller\Api;

use App\Exception\CustomException;
use App\Service\Api\Product\ApiProductService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/product')]
class ApiProductController extends AbstractController
{
    public function __construct(private readonly ApiProductService $service)
    {
    }

    /**
     * @throws CustomException
     */
    #[Route('/{slug}', name: 'app_product_variant', methods: ['GET'])]
    public function getProductVariant(string $slug): Response
    {
        return $this->render('pages/product/product_page.html.twig', [
            'data' => $this->service->getProductData($slug),
        ]);
    }
}
