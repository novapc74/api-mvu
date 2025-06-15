<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\IdentifierTrait;
use App\Repository\ProductRepository;
use App\Entity\Trait\UuidGeneratorTrait;

#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'IDX_PRODUCT_SLUG', columns: ['slug'])]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    use UuidGeneratorTrait;
    use IdentifierTrait;

    #[ORM\ManyToOne(targetEntity: Category::class, cascade: ['persist'], inversedBy: 'products')]
    private ?Category $category = null;

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
