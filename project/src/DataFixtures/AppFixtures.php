<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use ReflectionClass;
use ReflectionException;

abstract class AppFixtures extends Fixture
{
    protected ObjectManager $manager;

    abstract protected function loadData(ObjectManager $manager);

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadData($manager);
    }

    /**
     * @throws ReflectionException
     */
    protected function createEntity(string $className, int $count, callable $factory): void
    {
        $shortClass = (new ReflectionClass($className))->getShortName();

        for ($i = 0; $i < $count; $i++) {
            $entity = new $className();
            $factory($entity, $i);
            $this->manager->persist($entity);
            $this->addReference("{$shortClass}_$i", $entity);
        }
    }

    protected static function generateName(string $name, int $index): string
    {
        return $name . ++$index;
    }
}
