<?php

namespace App\Service\Category;

use App\Exception\CustomException;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\Api\Product\Dto\ProductsByCategorySlugDto;
use App\Service\Api\Product\Features\ProcessedProductCard;

readonly class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ProductRepository  $productRepository,
    )
    {
    }

    public function getMainCategories(): array
    {
        return $this->categoryRepository->getMainCategories();
    }

    /**
     * @throws CustomException
     */
    public function getProductsByCategorySlug(string $slug): ?array
    {
        #TODO убрать сложный запрос, фильры запрашивать через fetch(), при наведении на карточку с небольшой задержкой.
        $products = $this->productRepository->resolveSql(
            ProductsByCategorySlugDto::init($slug)
        );

        $catalog = json_decode($products['products'], true);

        #TODO это не нужно будет при упрощении запроса... перенесем для fetch() по api.
        foreach ($catalog as &$product) {
            ProcessedProductCard::processed($product);
        }

        return $catalog;
    }

}
