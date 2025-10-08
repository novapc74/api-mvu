<?php

namespace App\DataFixtures;

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

    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        // Получаем все продукты (предполагается 324)
        $products = [];
        for ($i = 0; $i < 324; $i++) {
            $products[] = $this->getReference("Product_$i", Product::class);
        }

        // Предполагаемые количества для комбинаций (на основе типичных данных; адаптируйте под реальные фикстуры)
        $sizeCount = 7;
        $genderCount = 3;
        $colorCount = 9;
        $warehouseCount = 3;
        $variantCountPerProduct = $sizeCount + $genderCount + $colorCount; // 19
        $totalVariants = 324 * $variantCountPerProduct; // 6156 вариантов

        // Создаем ProductVariant
        $this->createEntity(ProductVariant::class, $totalVariants, function (ProductVariant $variant, $index) use ($products, $sizeCount, $genderCount, $colorCount, $variantCountPerProduct) {
            $productIndex = intdiv($index, $variantCountPerProduct);
            $variantIndex = $index % $variantCountPerProduct;
            $product = $products[$productIndex];

            // Генерируем комбинации: size, gender, color (циклически для 19 вариантов)
            $sizeIndex = $variantIndex % $sizeCount;
            $genderIndex = intdiv($variantIndex, $sizeCount) % $genderCount;
            $colorIndex = intdiv($variantIndex, $sizeCount * $genderCount) % $colorCount;

            $size = $this->getReference("Size_$sizeIndex", Size::class);
            $gender = $this->getReference("Gender_$genderIndex", Gender::class);
            $color = $this->getReference("Color_$colorIndex", Color::class);

            $popularityIndexes = range(0, $variantCountPerProduct - 1);
            $variant
                ->setProduct($product)
                ->setSize($size)
                ->setGender($gender)
                ->setColor($color)
                ->setPopularityIndex(array_shift($popularityIndexes));
        });

        // Создаем Stock для каждого ProductVariant в каждом Warehouse
        $totalStock = $totalVariants * $warehouseCount; // 6156 * 3 = 18468 записей
        $this->createEntity(Stock::class, $totalStock, function ($stock, $index) use ($warehouseCount) {
            $variantIndex = intdiv($index, $warehouseCount);
            $warehouseIndex = $index % $warehouseCount;

            $variant = $this->getReference("ProductVariant_$variantIndex", ProductVariant::class);
            $warehouse = $this->getReference("Warehouse_$warehouseIndex", Warehouse::class);

            $stock->setValue(rand(0, 100)); // Случайное значение остатков (0-100)
            $stock->setProductVariant($variant);
            $stock->setWarehouse($warehouse);
        });

        $manager->flush();
    }
}
