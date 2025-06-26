<?php

namespace App\Service\Security;

use App\Entity\User;
use App\Exception\CustomException;
use App\Model\User\UserRegisterDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class SecurityService
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    )
    {
    }

    /**
     * @throws CustomException
     */
    public function register(UserRegisterDto $dto): array
    {
        if ($this->entityManager->getRepository(User::class)->findOneBy(['email' => $dto->email])) {
            throw new CustomException('Невалидная почта, используйте иную.');
        }

        $user = new User();

        $user
            ->setEmail($dto->email)
            ->setPassword($this->passwordHasher
                ->hashPassword(
                    $user,
                    $dto->password
                )
            );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return self::toArray($user);
    }

    private static function toArray(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ];
    }

}
