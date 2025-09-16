<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategoryThirdLevelFixtures extends AppFixtures implements DependentFixtureInterface
{
    protected function createEntity(string $className, int $count, callable $factory): void
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = new $className();
            $factory($entity, $i);
            $this->manager->persist($entity);
            $class = explode('\\', $className);
            $this->addReference('ThirdLevel_' . end($class) . '_' . $i, $entity);
        }
    }

    protected function loadData(ObjectManager $manager): void
    {
        $this->createEntity(Category::class, 27, function (Category $category, $count) {
            $category->setName(self::generateName('3. Категория_', $count));

            if ($count < 3) {
                $category->setCategory($this->getReference('SecondLevel_Category_0', Category::class));
            } elseif ($count < 6) {
                $category->setCategory($this->getReference('SecondLevel_Category_1', Category::class));
            } elseif ($count < 9) {
                $category->setCategory($this->getReference('SecondLevel_Category_2', Category::class));
            } elseif ($count < 12) {
                $category->setCategory($this->getReference('SecondLevel_Category_3', Category::class));
            } elseif ($count < 15) {
                $category->setCategory($this->getReference('SecondLevel_Category_4', Category::class));
            } elseif ($count < 18) {
                $category->setCategory($this->getReference('SecondLevel_Category_5', Category::class));
            } elseif ($count < 21) {
                $category->setCategory($this->getReference('SecondLevel_Category_6', Category::class));
            } elseif ($count < 24) {
                $category->setCategory($this->getReference('SecondLevel_Category_7', Category::class));
            } else {
                $category->setCategory($this->getReference('SecondLevel_Category_8', Category::class));
            }
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
            CategorySecondLevelFixtures::class
        ];
    }
}
