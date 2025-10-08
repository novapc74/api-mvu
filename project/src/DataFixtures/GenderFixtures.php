<?php

namespace App\DataFixtures;

use App\Entity\Gender;
use ReflectionException;
use Doctrine\Persistence\ObjectManager;

class GenderFixtures extends AppFixtures
{
    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        $genderNames = array_values(Gender::getAvailableGenders());
        $this->createEntity(Gender::class, 3, function (Gender $gender, int $count) use ($genderNames): void {
            $gender
                ->setGender($genderNames[$count]);
        });

        $manager->flush();
    }
}
