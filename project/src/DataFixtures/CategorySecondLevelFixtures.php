<?php

namespace App\DataFixtures;

use App\Entity\Category;
use ReflectionException;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategorySecondLevelFixtures extends AppFixtures implements DependentFixtureInterface
{
    /**
     * @throws ReflectionException
     */
    protected function createEntity(string $className, int $count, callable $factory): void
    {
        $shortClass = self::getShortClassName($className);

        for ($i = 0; $i < $count; $i++) {
            $entity = new $className();
            $factory($entity, $i);
            $this->manager->persist($entity);
            $this->addReference("SecondLevel_{$shortClass}_{$i}", $entity);
        }
    }

    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        $this->createEntity(Category::class, 9, function (Category $category, int $i) {
            $category->setName(self::generateName('2. Категория_', $i));

            // Определяем индекс родительской категории по интервалам
            $parentIndex = intdiv($i, 3); // 0 для 0..2, 1 для 3..5, 2 для 6..8

            $category->setCategory($this->getReference("Category_{$parentIndex}", Category::class));
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
