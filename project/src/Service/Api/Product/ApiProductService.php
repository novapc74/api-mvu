<?php

namespace App\Service\Api\Product;

use App\Entity\Size;
use App\Exception\CustomException;
use App\Repository\ProductRepository;
use App\Service\Api\Product\Dto\ProductPageDto;
use App\Service\Api\Product\Features\ProcessedProductCard;

readonly class ApiProductService
{
    public function __construct(
        private ProductRepository $productRepository,
    )
    {
    }

    /**
     * @throws CustomException
     */
    public function getProductData(string $slug): array
    {
        $data = $this->productRepository->getProductPageData(ProductPageDto::init($slug));

        ProcessedProductCard::processed($data);

        return $data;
    }
}
