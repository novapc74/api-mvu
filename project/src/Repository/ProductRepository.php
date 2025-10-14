<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\QueryBuilder;
use App\Exception\CustomException;
use App\Service\Paginator\Paginator;
use App\Model\Product\ProductSearchDto;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Api\Product\Interface\SqlInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getProductsByCategorySlug(string $categorySlug): array
    {

    }

    /**
     * @throws CustomException
     */
    public function resolveSql(SqlInterface $dto): array
    {
        $sql = $dto->getSql();
        $param = $dto->getParam();
        $type = $dto->getType();

        try {
            $result =  $this->getEntityManager()
                ->getConnection()
                ->executeQuery($sql, $param, $type)
                ->fetchAssociative();

        } catch (Exception $exception) {
            throw new CustomException($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (is_array($result)) {
            return $result;
        }

        throw new NotFoundHttpException('Товар не найден');
    }

    public function getProductCount(?ProductSearchDto $dto = null): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p)');

        if ($dto !== null) {
            $this->resolveFilters($dto, $qb);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getProduct(string $slug, ?Cart $cart = null): ?array
    {
        $qb = $this->createQueryBuilder('p')
            ->select([
                'p.id',
                'p.name',
                'p.slug',
            ])
            ->andWhere('p.slug = :slug')
            ->setParameter('slug', $slug);

        if ($cart) {
            $qb->addSelect('COALESCE(ci.quantity, 0) AS quantity')
                ->leftJoin('p.cartItems', 'ci', 'WITH', 'ci.cart = :cartId')
                ->setParameter('cartId', $cart->getId()->toRfc4122(), UuidType::NAME);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getProducts(Paginator $paginator, ?Cart $cart = null, ?ProductSearchDto $dto = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select([
                'p.id',
                'p.name',
                'p.slug'
//                'p.popularityIndex'
            ])
//            ->orderBy('p.popularityIndex', 'DESC')
        ;

        if ($cart) {
            $qb->addSelect('COALESCE(ci.quantity, 0) AS quantity')
                ->leftJoin('p.cartItems', 'ci', 'WITH', 'ci.cart = :cartId')
                ->setParameter('cartId', $cart->getId()->toRfc4122(), UuidType::NAME);
        }

        if ($dto !== null) {
            $this->resolveFilters($dto, $qb);
        }

        $paginator->paginateQueryBuilder($qb);

        return $qb->getQuery()->getResult();
    }

    private function resolveFilters(ProductSearchDto $dto, QueryBuilder $qb): void
    {
        if ($search = $dto->search) {
            $qb->andWhere('LOWER(p.name) LIKE :search')
                ->setParameter('search', '%' . trim(mb_strtolower($search)) . '%');
        }
    }
}
