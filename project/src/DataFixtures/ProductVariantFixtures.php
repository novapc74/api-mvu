<?php

namespace App\DataFixtures;

use App\Entity\ProductProperty;
use App\Entity\Property;
use App\Entity\Size;
use App\Entity\Color;
use App\Entity\Stock;
use App\Entity\Gender;
use App\Entity\Product;
use ReflectionException;
use App\Entity\Warehouse;
use App\Entity\ProductVariant;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductVariantFixtures extends AppFixtures implements DependentFixtureInterface
{
    private const SIZE_COUNT = 7;
    private const GENDER_COUNT = 3;
    private const COLOR_COUNT = 9;
    private const WAREHOUSE_COUNT = 3;
    private const PRODUCT_COUNT = 324;

    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        /** Получаем все продукты */
        $products = [];
        for ($i = 0; $i < self::PRODUCT_COUNT; $i++) {
            $products[] = $this->getReference("Product_$i", Product::class);
        }

        $variantCountPerProduct = self::SIZE_COUNT + self::GENDER_COUNT + self::COLOR_COUNT;
        $totalVariants = self::PRODUCT_COUNT * $variantCountPerProduct; // 324 * 6156 вариантов

        /** Создаем ProductVariant */
        $this->createEntity(ProductVariant::class, $totalVariants, function (ProductVariant $variant, $index) use ($products, $variantCountPerProduct) {
            $productIndex = intdiv($index, $variantCountPerProduct);
            $variantIndex = $index % $variantCountPerProduct;
            $product = $products[$productIndex];

            /** Генерируем комбинации: size, gender, color (циклически для 19 вариантов) */
            $sizeIndex = $variantIndex % self::SIZE_COUNT;
            $genderIndex = intdiv($variantIndex, self::SIZE_COUNT) % self::GENDER_COUNT;
            $colorIndex = intdiv($variantIndex, self::COLOR_COUNT) % self::COLOR_COUNT;

            $size = $this->getReference("Size_$sizeIndex", Size::class);
            $gender = $this->getReference("Gender_$genderIndex", Gender::class);
            $color = $this->getReference("Color_$colorIndex", Color::class);

            $variant
                ->setProduct($product)
                ->setSize($size)
                ->setGender($gender)
                ->setColor($color)
                ->setPopularityIndex(rand(0, 100));
        });

        /** Создаем Stock для каждого ProductVariant в каждом Warehouse */
        $totalStock = $totalVariants * self::WAREHOUSE_COUNT;
        $this->createEntity(Stock::class, $totalStock, function (Stock $stock, $index) {

            $variantIndex = intdiv($index, self::WAREHOUSE_COUNT);
            $warehouseIndex = $index % self::WAREHOUSE_COUNT;

            $variant = $this->getReference("ProductVariant_$variantIndex", ProductVariant::class);
            $warehouse = $this->getReference("Warehouse_$warehouseIndex", Warehouse::class);

            $stock
                ->setPrice(rand(0, 100))
                ->setAmount(rand(0, 150))
                ->setProductVariant($variant)
                ->setWarehouse($warehouse);
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
            SizeFixtures::class,
            GenderFixtures::class,
            ColorFixtures::class,
            WarehouseFixtures::class,
        ];
    }
}
