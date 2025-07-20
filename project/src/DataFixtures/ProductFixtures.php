<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Faker\Generator;

class ProductFixtures extends AppFixtures implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }


    protected function loadData(ObjectManager $manager)
    {
        $this->createEntity(Product::class, 9, function (Product $product, $count) {
            $product
                ->setName($this->faker->title());

            if ($count <= 2) {
                $category = $this->getReference('Category_0', Category::class);
            } elseif ($count <= 5) {
                $category = $this->getReference('Category_1', Category::class);
            } else {
                $category = $this->getReference('Category_2', Category::class);
            }

            $product->setCategory($category);
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
