<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<CartItem>
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    public function findCartItemByProduct(Cart $cart, Product $product): ?CartItem
    {
        return $this->createQueryBuilder('ci')
            ->andWhere('ci.product = :product')
            ->andWhere('ci.cart = :cart')
            ->setParameter('product', $product->getId(), UuidType::NAME)
            ->setParameter('cart', $cart->getId(), UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getCartItemsDataForResponse(Cart $cart): array
    {
        #TODO по мере развития, добавить новые поля...

        return $this->createQueryBuilder('ci')
            ->select([
                'p.id AS productId',
                'ci.quantity',
            ])
            ->innerJoin('ci.cart', 'c')
            ->innerJoin('ci.product', 'p')
            ->andWhere('ci.cart = :cart')
            ->setParameter('cart', $cart)
            ->getQuery()
            ->getArrayResult();
    }
}
