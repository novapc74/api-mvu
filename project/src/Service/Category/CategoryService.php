<?php

namespace App\Service\Category;

use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private CategoryRepository $categoryRepository,
    )
    {
    }

    public function getMainCategories(): array
    {
        return $this->categoryRepository->getMainCategories();
    }

}
