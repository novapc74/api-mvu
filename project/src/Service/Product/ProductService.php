<?php

namespace App\Service\Product;

use App\Service\Cart\CartHelper;
use App\Service\Paginator\Paginator;
use App\Repository\ProductRepository;
use App\Model\Product\ProductSearchDto;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use App\Service\Paginator\PaginatorResponseDto;

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
    public function getProducts(?ProductSearchDto $dto = null): array
    {
        $count = $this->productRepository->getProductCount($dto);
        $collection = $this->productRepository->getProducts($this->paginator, $this->cartHelper->getCart(), $dto);

        #TODO SQL- вариант
        # $sqlDto = ProductCatalogSqlDto::init($this->paginator, $this->cartHelper->getCart());
        # $collection = $this->productRepository->getSqlProducts($sqlDto);

        return PaginatorResponseDto::response(
            $this->paginator->paginate($collection, $count)
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function getProductPage(string $slug): array
    {
        $product = $this->productRepository->getProduct($slug, $this->cartHelper->getCart());

        return [
            'breadcrumbs' => 'пока думаю, как лучше собрать...',
            'product' => $product,
        ];
    }
}
