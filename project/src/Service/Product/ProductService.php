<?php

namespace App\Service\Product;

use App\Entity\Product;
use App\Service\Paginator\Paginator;
use App\Repository\ProductRepository;
use App\Service\Paginator\PaginatorResponseDto;

readonly class ProductService
{
    public function __construct(
        private ProductRepository $productRepository,
        private Paginator         $paginator,
    )
    {
    }

    public function getProducts(): array
    {
        $count = $this->productRepository->getProductCount();
        $collection = $this->productRepository->getProducts($this->paginator);

        return PaginatorResponseDto::response(
            $this->paginator->paginate($collection, $count)
        );
    }

    public function getProductPage(Product $product): array
    {
        return [
            'breadcrumbs' => '',
            'product' => [
                'id' => $product->getId()->toRfc4122(),
                'name' => $product->getName(),
            ],
            'category' => [
                'id' => $product->getCategory()->getId()->toRfc4122(),
                'name' => $product->getCategory()->getName(),
            ],
        ];
    }
}
