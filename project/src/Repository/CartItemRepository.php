<?php

namespace App\Repository;

use App\Entity\CartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<CartItem>
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    public function findCartItemByProduct(string $cartId, string $productId): ?CartItem
    {
        return $this->createQueryBuilder('ci')
            ->innerJoin('ci.cart', 'c')
            ->innerJoin('ci.product', 'p')
            ->andWhere('p.id =:productId')
            ->andWhere('c.id =:cartId')
            ->setParameter('productId', $productId, UuidType::NAME)
            ->setParameter('cartId', $cartId, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
