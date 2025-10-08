<?php

namespace App\DataFixtures;

use App\Entity\Property;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use ReflectionException;
use App\Entity\ProductProperty;
use Doctrine\Persistence\ObjectManager;

class ProductPropertyFixtures extends AppFixtures implements DependentFixtureInterface
{
    private const PROPERTY_VALUES = [
        '95', '5', '180'
    ];

    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        $this->createEntity(ProductProperty::class, 3, function (ProductProperty $productProperty, int $count) {

            $property = $this->getReference("Property_$count", Property::class);

            $productProperty
                ->setProperty($property)
                ->setValue(self::PROPERTY_VALUES[$count])
                ->setScale($count);
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PropertyFixtures::class
        ];
    }
}
