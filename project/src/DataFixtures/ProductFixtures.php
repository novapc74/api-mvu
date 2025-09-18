<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use ReflectionException;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends AppFixtures implements DependentFixtureInterface
{
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

            $product->setName(self::generateName("{$name}_", $count));

            $product->setCategory($thirdLevelCategories[$index]);

            #TODO Можно добавить другие свойства товара, если есть
        });

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
        ];
    }
}
