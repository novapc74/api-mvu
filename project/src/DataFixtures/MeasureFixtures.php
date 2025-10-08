<?php

namespace App\DataFixtures;

use App\Entity\Measure;
use ReflectionException;
use Doctrine\Persistence\ObjectManager;

class MeasureFixtures extends AppFixtures
{

    private const MEASURE_DATA = [
        '%', 'г/м²'
    ];

    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        $this->createEntity(Measure::class, 2, function (Measure $measure, int $count): void {
            $measure->setMeasure(self::MEASURE_DATA[$count]);
        });
        $manager->flush();
    }
}
