<?php

namespace App\Controller\Api;

use App\Model\Product\ProductSearchDto;
use App\Service\Product\ProductService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ApiResponse\ApiResponseFactory;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class ApiSearchController extends AbstractController
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/product/search', name: 'api_search', methods: ['GET'])]
    public function searchProduct(
        #[MapQueryString] ProductSearchDto $dto,
        ProductService                        $service): Response
    {
        $html = $this->renderView('_embed/search/search_result.html.twig', [
            'data' => $service->getProducts($dto)
        ]);

        return ApiResponseFactory::responseHelper($html);
    }
}
