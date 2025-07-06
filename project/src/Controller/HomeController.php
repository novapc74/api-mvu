<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $haystack = ['info', 'success', 'error', 'warning'];
        $randomKey = array_rand($haystack);

        return $this->render('home/index.html.twig', [
            'type' => $haystack[$randomKey],
            'message' => 'Hello world!'
        ]);
    }
}
