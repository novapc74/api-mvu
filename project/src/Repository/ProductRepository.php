<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\Product;
use App\Model\Product\ProductSearchDto;
use App\Service\Paginator\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getProductCount(ProductSearchDto $dto): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p)');

        $this->resolveFilters($dto, $qb);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getProducts(ProductSearchDto $dto, Paginator $paginator, ?Cart $cart = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select([
                'p.id',
                'p.name',
                'p.slug',
            ]);

        if ($cart) {
            $qb->addSelect('COALESCE(ci.quantity, 0) AS quantity')
                ->leftJoin('p.cartItems', 'ci', 'WITH', 'ci.cart = :cartId')
                ->setParameter('cartId', $cart->getId()->toRfc4122(), UuidType::NAME);
        }

        $this->resolveFilters($dto, $qb);

        $paginator->paginateQueryBuilder($qb);

        return $qb->getQuery()->getResult();
    }

    private function resolveFilters(ProductSearchDto $dto, QueryBuilder $qb): void
    {
        if ($search = $dto->search) {
            $qb->andWhere('LOWER(p.name) LIKE :search')
                ->setParameter('search', '%' . mb_strtolower($search) . '%');
        }
    }
}
