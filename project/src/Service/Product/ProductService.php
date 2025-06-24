<?php

namespace App\Service\Product;

use App\Entity\Product;
use App\Model\Product\ProductIdDto;
use App\Repository\ProductRepository;

class ProductService
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    public function getProducts(array $dtoCollection): array
    {
        #TODO прилетают uuid товаров. Дальше с этим что-то нужно будет сделать :)
        return array_map(fn(ProductIdDto $dto) => $dto->id->toRfc4122(), $dtoCollection);
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
