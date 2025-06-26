<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends AppFixtures
{
    private const USER_AUTH = [
        'email' => 'admin@admin.com',
        'password' => 'admin',
        'roles' => ['ROLE_ADMIN', 'ROLE_USER']
    ];

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    protected function loadData(ObjectManager $manager): void
    {
        if (!$manager->getRepository(User::class)->findOneBy(['email' => 'admin@admin.com'])) {

            $this->createEntity(User::class, 1, function (User $user) {
                $user
                    ->setEmail(self::USER_AUTH['email'])
                    ->setRoles(self::USER_AUTH['roles'])
                    ->setPassword($this->passwordHasher->hashPassword($user, self::USER_AUTH['password']));
            });

            $manager->flush();
        }
    }
}
