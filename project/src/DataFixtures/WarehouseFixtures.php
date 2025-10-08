<?php

namespace App\DataFixtures;

use ReflectionException;
use App\Entity\Warehouse;
use Doctrine\Persistence\ObjectManager;

class WarehouseFixtures extends AppFixtures
{
    private const WAREHOUSE_DATA = [
        [
            'name' => 'склад № 1',
            'address' => 'Санкт-Петербург, Нарвский проспект дом 1, корпус 3, офис 325',
        ],
        [
            'name' => 'склад № 2',
            'address' => 'Москва, Горьковский проспект дом 15, офис 325',
        ],
        [
            'name' => 'склад № 3',
            'address' => 'Самара, ул.Набержная дом 23, офис 325',
        ],
    ];

    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        $this->createEntity(Warehouse::class, 3, function (Warehouse $warehouse, int $count) {
            $warehouse
                ->setName(self::WAREHOUSE_DATA[$count]['name'])
                ->setAddress(self::WAREHOUSE_DATA[$count]['address']);
        });
        $manager->flush();
    }
}
