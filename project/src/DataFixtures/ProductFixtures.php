<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Property;
use ReflectionException;
use App\Entity\ProductProperty;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends AppFixtures implements DependentFixtureInterface
{
    private const PROPERTY_VALUES = [
        '95', '5', '180'
    ];

    private const THIRD_LEVEL_CATEGORIES_COUNT = 27;
    private const PRODUCT_COUNT = 324;
    private const PROPERTY_COUNT = 3;

    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        $thirdLevelCategories = array_map(
            fn(int $i) => $this->getReference("ThirdLevel_Category_$i", Category::class),
            range(0, self::THIRD_LEVEL_CATEGORIES_COUNT - 1)
        );

        /** Для каждой категории создаём 12 товаров */
        $this->createEntity(Product::class, self::PRODUCT_COUNT, function (Product $product, int $count) use ($thirdLevelCategories) {

            $index = intdiv($count - 1, 12);
            $name = mb_ucfirst(self::getName($index));

            $product
                ->setName(self::generateName("{$name}_", $count))
                ->setCategory($thirdLevelCategories[$index]);
        });

        $products = [];
        for ($i = 0; $i < self::PRODUCT_COUNT; $i++) {
            $products[] = $this->getReference("Product_$i", Product::class);
        }

        $properties = [];
        for ($i = 0; $i < self::PROPERTY_COUNT; $i++) {
            $properties[] = $this->getReference("Property_$i", Property::class);
        }

        /** Создаем ProductProperty для каждого продукта */
        foreach ($products as $product) {

            foreach ($properties as $key => $property) {

                $productProperty = (new ProductProperty())
                    ->setProduct($product)
                    ->setProperty($property)
                    ->setScale($key)
                    ->setValue(self::PROPERTY_VALUES[$key]);

                $manager->persist($productProperty);
            }
        }

        $manager->flush();
    }

    private static function getName($index): string
    {
        $items = [
            "футболка",
            "майка",
            "топ",
            "водолазка",
            "свитер",
            "кофта",
            "кардиган",
            "пуловер",
            "худи",
            "толстовка",
            "джемпер",
            "блузка",
            "туника",
            "платье",
            "юбка",
            "шорты",
            "леггинсы",
            "колготки",
            "носки",
            "гольфы",
            "шапка",
            "шарф",
            "перчатки",
            "варежки",
            "шарф-хомут",
            "бандана",
            "повязка на голову"
        ];

        return $items[$index];
    }

    public function getDependencies(): array
    {
        return [
            CategoryThirdLevelFixtures::class,
            PropertyFixtures::class,
        ];
    }
}
