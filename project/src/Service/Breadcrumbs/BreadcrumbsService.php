<?php

namespace App\Service\Breadcrumbs;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\DBAL\Exception;

readonly class BreadcrumbsService
{
    public function __construct(
        private CategoryRepository $categoryRepository
    )
    {
    }

    /**
     * @throws Exception
     */
    public function getBreadcrumbsByCategory(string $categorySlug): array
    {
        return $this->categoryRepository->getBreadCrumbsDataByCategory($categorySlug);
    }
}
