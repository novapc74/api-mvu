<?php

namespace App\Repository;

use App\Entity\Size;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Size>
 */
class SizeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Size::class);
    }

    public function getSizeNameCollection(): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s.size')
            ->orderBy('s.id')
            ->getQuery()->getResult();

        return array_column($qb, 'size');
    }
}
