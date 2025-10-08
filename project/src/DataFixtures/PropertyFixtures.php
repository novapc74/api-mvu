<?php

namespace App\DataFixtures;

use App\Entity\Measure;
use App\Entity\Property;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use ReflectionException;
use Doctrine\Persistence\ObjectManager;

class PropertyFixtures extends AppFixtures implements DependentFixtureInterface
{
    private const PROPERTY_DATA = [
        'хлопок', 'полиестер', 'плотность'
    ];

    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        $this->createEntity(Property::class, 3, function (Property $property, int $count) {

            $index = $count < 2 ? 0 : 1;
            $measure = $this->getReference("Measure_$index", Measure::class);

            $property
                ->setName(self::PROPERTY_DATA[$count])
                ->setMeasure($measure);
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MeasureFixtures::class
        ];
    }
}
