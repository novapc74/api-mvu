<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategorySecondLevelFixtures extends AppFixtures implements DependentFixtureInterface
{
    protected function createEntity(string $className, int $count, callable $factory): void
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = new $className();
            $factory($entity, $i);
            $this->manager->persist($entity);
            $class = explode('\\', $className);
            $this->addReference('SecondLevel_' . end($class) . '_' . $i, $entity);
        }
    }

    protected function loadData(ObjectManager $manager): void
    {
        $this->createEntity(Category::class, 9, function (Category $category, $count) {
            $category->setName(self::generateName('2. Категория_', $count));

            if ($count < 3) {
                $category->setCategory($this->getReference('Category_0', Category::class));
            } elseif ($count < 6) {
                $category->setCategory($this->getReference('Category_1', Category::class));
            } else {
                $category->setCategory($this->getReference('Category_2', Category::class));
            }
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
