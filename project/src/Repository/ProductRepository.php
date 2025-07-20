<?php

namespace App\Repository;

use App\Entity\Product;
use App\Service\Paginator\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function getProducts(Paginator $paginator): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select([
                'p.id',
                'p.name',
                'p.slug',
            ]);

        $paginator->resolveQueryBuilder($qb);

        return $qb->getQuery()->getResult();
    }
}
