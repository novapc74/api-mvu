<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\DBAL\Exception;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Breadcrumbs\BreadcrumbsService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CategoryController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/category/{slug}', name: 'app_category')]
    public function index(
        BreadcrumbsService                                 $breadcrumbsService,
        #[MapEntity(mapping: ['slug' => 'slug'])] Category $category
    ): Response
    {
        return $this->render('category/index.html.twig', [
            #TODO закешировать breadcrumbs ...
            'breadcrumbs' => $breadcrumbsService->getBreadcrumbsByCategory($category)
        ]);
    }
}
