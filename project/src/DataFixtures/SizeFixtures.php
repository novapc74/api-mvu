<?php

namespace App\DataFixtures;

use App\Entity\Size;
use ReflectionException;
use Doctrine\Persistence\ObjectManager;

class SizeFixtures extends AppFixtures
{
    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        $sizeValue = array_values(Size::getAvailableSizes());
        $this->createEntity(Size::class, 7, function (Size $size, $count) use ($sizeValue) {
            $size
                ->setSize($sizeValue[$count]);
        });

        $manager->flush();
    }
}
