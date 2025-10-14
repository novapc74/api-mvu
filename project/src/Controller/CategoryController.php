<?php

namespace App\Controller;

use App\Entity\Category;
use App\Exception\CustomException;
use App\Service\Category\CategoryService;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Breadcrumbs\BreadcrumbsService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService    $service,
        private readonly BreadcrumbsService $breadcrumbsService)
    {
    }

    /**
     * @throws Exception
     * @throws CustomException
     */
    #[Route('/category/{slug}', name: 'app_category')]
    public function index(?string $slug): Response
    {
        if (!is_string($slug)) {
            throw new BadRequestHttpException();
        }

//        $breadcrumbs = $this->breadcrumbsService->getBreadcrumbsByCategory($slug);
        $products = $this->service->getProductsByCategorySlug($slug);

        return $this->render('category/index.html.twig', [
//            'breadcrumbs' => $breadcrumbs,
            'products' => $products,
        ]);
    }
}
