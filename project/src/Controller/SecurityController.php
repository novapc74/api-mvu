<?php

namespace App\Controller;

use App\Exception\CustomException;
use App\Model\User\UserRegisterDto;
use App\Service\Security\SecurityService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/auth')]
final class SecurityController extends AbstractController
{
    public function __construct(private readonly SecurityService $service)
    {
    }

    /**
     * @throws CustomException
     */
    #[Route('/register', methods: ['POST'])]
    public function index(#[MapRequestPayload] UserRegisterDto $dto): JsonResponse
    {
        return new JsonResponse($this->service->register($dto), Response::HTTP_CREATED);
    }
}
