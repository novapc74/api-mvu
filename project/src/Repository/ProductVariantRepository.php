<?php

namespace App\Repository;

use App\Entity\ProductVariant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductVariant>
 */
class ProductVariantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductVariant::class);
    }

    public function getProductVariantData(string $productId, int $productVariantId): array
    {
        return $this->createQueryBuilder('pv')
            ->select([
                'pv.id',
                'p.id as productId',
                'pv.popularityIndex',
                's.size',
                "CONCAT(c.name, '-',  c.hexCode) as color",
                'g.gender',
            ])
            ->andWhere('pv.id = :productVariantId')
            ->setParameter('productVariantId', $productVariantId)
            ->innerJoin('pv.size', 's')
            ->innerJoin('pv.color', 'c')
            ->innerJoin('pv.gender', 'g')
            ->innerJoin('pv.product', 'p')
//            ->leftJoin('pv.stocks', 'st')
            ->getQuery()
            ->getArrayResult();
    }
}
