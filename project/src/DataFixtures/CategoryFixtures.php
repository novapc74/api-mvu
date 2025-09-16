<?php

namespace App\DataFixtures;

use App\Entity\Category;
use ReflectionException;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategoryFixtures extends AppFixtures implements DependentFixtureInterface
{
    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        $this->createEntity(Category::class, 3, function (Category $category, $count) {
            $category
                ->setName(self::generateName('1. Категория_', $count));
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
