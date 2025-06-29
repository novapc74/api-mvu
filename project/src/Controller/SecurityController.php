<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\CustomException;
use App\Model\User\UserRegisterDto;
use App\Service\Security\SecurityService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/auth')]
final class SecurityController extends AbstractController
{
    public function __construct(private readonly SecurityService $service)
    {
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (null === $user) {
            return new JsonResponse([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $this->json([
            'username' => $user->getUserIdentifier(),
        ]);
    }

    /**
     * @throws CustomException
     */
    #[Route('/register', methods: ['POST'])]
    public function register(#[MapRequestPayload] UserRegisterDto $dto): JsonResponse
    {
        return new JsonResponse($this->service->register($dto), Response::HTTP_CREATED);
    }
}
