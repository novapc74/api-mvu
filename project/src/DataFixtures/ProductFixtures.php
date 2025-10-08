<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\ProductProperty;
use App\Entity\Property;
use ReflectionException;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends AppFixtures implements DependentFixtureInterface
{
    private const PROPERTY_VALUES = [
        '95', '5', '180'
    ];

    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        $thirdLevelCategories = array_map(
            fn(int $i) => $this->getReference("ThirdLevel_Category_$i", Category::class),
            range(0, 26)
        );


        /** Для каждой категории создаём 12 товаров */
        $this->createEntity(Product::class, 324, function (Product $product, int $count) use ($thirdLevelCategories) {
            $index = intdiv($count - 1, 12);

            $name = mb_ucfirst(self::getName($index));

            $product
                ->setName(self::generateName("{$name}_", $count));

            $product->setCategory($thirdLevelCategories[$index]);
        });

        $products = [];
        for ($i = 0; $i < 324; $i++) {
            $products[] = $this->getReference("Product_$i", Product::class);
        }

        $properties = [];
        for ($i = 0; $i < 3; $i++) {
            $properties[] = $this->getReference("Property_$i", Property::class);
        }

        // Создаем ProductProperty для каждого продукта
        // Для каждого продукта выбираем 3-5 случайных свойств
        foreach ($products as $product) {

            foreach ($properties as $key => $property) {

                $productProperty = new ProductProperty();
                $productProperty->setProduct($product);
                $productProperty->setProperty($property);
                $productProperty->setScale($key); // Случайный порядок сортировки (scale)
                $productProperty->setValue(self::PROPERTY_VALUES[$key]); // Случайное значение (адаптируйте под тип свойства, если нужно)

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
