<?php

namespace App\Service\Product;

use App\Entity\Product;
use App\Model\Product\ProductSearchDto;
use Doctrine\DBAL\Exception;
use App\Service\Cart\CartHelper;
use App\Service\Paginator\Paginator;
use App\Repository\ProductRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use App\Service\Paginator\PaginatorResponseDto;
use App\Service\Product\Adapter\ProductCatalogSqlDto;

readonly class ProductService
{
    public function __construct(
        private ProductRepository $productRepository,
        private Paginator         $paginator,
        private CartHelper        $cartHelper,
    )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function getProducts(ProductSearchDto $dto): array
    {
        $count = $this->productRepository->getProductCount($dto);
        $collection = $this->productRepository->getProducts($dto, $this->paginator, $this->cartHelper->getCart());

        #TODO SQL- вариант
        # $sqlDto = ProductCatalogSqlDto::init($this->paginator, $this->cartHelper->getCart());
        # $collection = $this->productRepository->getSqlProducts($sqlDto);

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
