<?php

namespace App\Controller;

use App\Entity\Category;
use App\Service\Breadcrumbs\BreadcrumbsService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/category/{slug}', name: 'app_category')]
    public function index(BreadcrumbsService $breadcrumbsService, Category $category): Response
    {
        return $this->render('category/index.html.twig', [
            'breadcrumbs' => $breadcrumbsService->getBreadcrumbsByCategory($category)
        ]);
    }
}
