<?php

namespace App\Service\Category;

use App\Repository\CategoryRepository;

readonly class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    )
    {
    }

    public function getMainCategories(): array
    {
        return $this->categoryRepository->getMainCategories();
    }

}
