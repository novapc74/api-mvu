<?php

namespace App\DataFixtures;

use App\Entity\Color;
use ReflectionException;
use Doctrine\Persistence\ObjectManager;

class ColorFixtures extends AppFixtures
{

    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        $values = array_values(Color::getAvailableColors());
        $keys = array_keys(Color::getAvailableColors());

        $this->createEntity(Color::class, 9, function (Color $color, int $count) use ($keys, $values) {
            $color
                ->setName($keys[$count])
                ->setHexCode($values[$count]);
        });

        $manager->flush();
        // TODO: Implement loadData() method.
    }
}
