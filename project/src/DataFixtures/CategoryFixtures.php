<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategoryFixtures extends AppFixtures implements DependentFixtureInterface
{
    private const PARENT_CATEGORY_DATA = [
        'Свитшоты',
        'Футболки',
        'Худи',
    ];

    protected function loadData(ObjectManager $manager): void
    {
        $this->createEntity(Category::class, 3, function (Category $category, $count) {
            $category
                ->setName(self::PARENT_CATEGORY_DATA[$count]);
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
