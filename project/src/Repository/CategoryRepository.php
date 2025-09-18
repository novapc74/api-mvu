<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @throws Exception
     */
    public function getBreadCrumbsDataByCategory(Category $category): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "
        WITH RECURSIVE category_chain AS (
            SELECT id, name, slug, category_id, 0 AS level
            FROM category
            WHERE id = UNHEX(REPLACE(:category_id, '-', ''))
            UNION ALL
            SELECT c.id, c.name, c.slug, c.category_id, cc.level + 1
            FROM category c
            JOIN category_chain cc ON c.id = cc.category_id
        )
        SELECT
            cc.id,
            cc.name AS category_name,
            cc.slug,
            cc.level,
            CASE WHEN cc.id = UNHEX(REPLACE(:category_id, '-', '')) THEN 1 ELSE 0 END AS is_active,  -- 1 (true) для текущей категории
            cc.category_id AS parent_category_id,
            CASE
                WHEN (SELECT COUNT(*) FROM category WHERE category_id = cc.id) > 0 THEN NULL
                ELSE (SELECT COUNT(*) FROM product WHERE category_id = cc.id)
            END AS product_count
        FROM category_chain cc
        ORDER BY cc.level DESC
    ";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('category_id', $category->getId());  // Строка UUID
        $result = $stmt->executeQuery();

        // Функция для конвертации бинарного UUID в строку
        $binaryToUuid = function ($binary) {
            if (is_resource($binary)) {
                $binary = stream_get_contents($binary);  // Если ресурс, читаем содержимое
            }
            if (!is_string($binary) || strlen($binary) !== 16) {
                return null;  // Ошибка, если не бинарные 16 байт
            }
            $hex = bin2hex($binary);
            return substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4) . '-' . substr($hex, 16, 4) . '-' . substr($hex, 20, 12);
        };

        $breadcrumbs = [];

        while ($row = $result->fetchAssociative()) {
            $id = $binaryToUuid($row['id']);
            $parentId = $row['parent_category_id'] ? $binaryToUuid($row['parent_category_id']) : null;

            $breadcrumbs[] = [
                'id' => $id,  // Строка UUID
                'category_name' => $row['category_name'],
                'slug' => $row['slug'],
                'product_count' => $row['product_count'] !== null ? (int)$row['product_count'] : null,
                'level' => (int)$row['level'],
                'is_active' => (bool)$row['is_active'],  // Теперь true для level 0
                'parent_category_id' => $parentId,  // Строка UUID или null
            ];
        }

        return $breadcrumbs;
    }
}
