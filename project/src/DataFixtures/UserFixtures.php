<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends AppFixtures
{
    protected function loadData(ObjectManager $manager)
    {
        if (!$manager->getRepository(User::class)->findOneBy(['email' => 'admin@admin.com'])) {

            $this->createEntity(User::class, 1, function (User $user) {
                $user
                    ->setEmail('admin@admin.com');
            });

            $manager->flush();
        }
    }
}
