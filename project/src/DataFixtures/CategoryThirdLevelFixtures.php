<?php

namespace App\DataFixtures;

use ReflectionClass;
use App\Entity\Category;
use ReflectionException;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategoryThirdLevelFixtures extends AppFixtures implements DependentFixtureInterface
{
    protected function createEntity(string $className, int $count, callable $factory): void
    {
        $shortClass = self::getShortClassName($className);

        for ($i = 0; $i < $count; $i++) {
            $entity = new $className();
            $factory($entity, $i);
            $this->manager->persist($entity);
            $this->addReference("ThirdLevel_{$shortClass}_$i", $entity);
        }
    }

    /**
     * @throws ReflectionException
     */
    protected function loadData(ObjectManager $manager): void
    {
        $parentRefs = array_map(
            fn(int $i) => $this->getReference("SecondLevel_Category_$i", Category::class),
            range(0, 8)
        );

        $this->createEntity(Category::class, 27, function (Category $category, $count) use ($parentRefs) {
            $category->setName(self::generateName('3. Категория_', $count));

            $parentIndex = intdiv($count - 1, 3);
            $category->setCategory($parentRefs[$parentIndex]);
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategorySecondLevelFixtures::class
        ];
    }
}
