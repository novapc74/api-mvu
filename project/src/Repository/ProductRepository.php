<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\Product;
use App\Service\Paginator\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function getProductCount(): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p)');


        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getProducts(Paginator $paginator, ?Cart $cart = null): array
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

        $paginator->paginateQueryBuilder($qb);

        return $qb->getQuery()->getResult();
    }
}
