<?php

namespace App\Controller;

use App\Entity\Category;
use App\Service\Breadcrumbs\BreadcrumbsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/category/{slug}', name: 'app_category')]
    public function index(BreadcrumbsService $breadcrumbsService, Category $category): Response
    {
        $breadcrumbs = $breadcrumbsService->getBreadcrumbsByCategory($category);

        dd($breadcrumbs);

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
}
