<?php

namespace App\Service\Product;

use App\Entity\ProductVariant;
use App\Service\Cart\CartHelper;
use App\Service\Paginator\Paginator;
use App\Repository\ProductRepository;
use App\Model\Product\ProductSearchDto;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use App\Service\Paginator\PaginatorResponseDto;
use Symfony\Component\Uid\Uuid;

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
        $breadcrumbs = 'пока думаю, как лучше собрать...';
        $cart = $this->cartHelper->getCart();
        $product = $this->productRepository->getProduct($slug, $cart);

        return compact('breadcrumbs', 'product');
    }
}
