<?php

namespace App\Controller;

use App\Service\Category\CategoryService;
use App\Service\Product\ProductService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/', name: 'app_home')]
    public function index(ProductService $productService, CategoryService $categoryService): Response
    {
        return $this->render('pages/home/index.html.twig', [
            'categories' => $categoryService->getMainCategories(),
            'data' => $productService->getProducts(),
        ]);
    }
}
