<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\IdentifierTrait;
use App\Repository\ProductRepository;
use App\Entity\Trait\UuidGeneratorTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'IDX_PRODUCT_SLUG', columns: ['slug'])]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    use UuidGeneratorTrait;
    use IdentifierTrait;

    #[ORM\ManyToOne(targetEntity: Category::class, cascade: ['persist'], inversedBy: 'products')]
    private ?Category $category = null;

    /**
     * @var Collection<int, ProductProperty>
     */
    #[ORM\OrderBy(['scale' => 'DESC'])]
    #[ORM\OneToMany(targetEntity: ProductProperty::class, mappedBy: 'product', cascade: ['persist', 'remove'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $productProperties;

    /**
     * @var Collection<int, ProductVariant>
     */
    #[ORM\OneToMany(targetEntity: ProductVariant::class, mappedBy: 'product', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $productVariants;

    public function __construct()
    {
        $this->productProperties = new ArrayCollection();
        $this->productVariants = new ArrayCollection();
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, ProductProperty>
     */
    public function getProductProperties(): Collection
    {
        return $this->productProperties;
    }

    public function addProductProperty(ProductProperty $productProperty): static
    {
        if (!$this->productProperties->contains($productProperty)) {
            $this->productProperties->add($productProperty);
            $productProperty->setProduct($this);
        }

        return $this;
    }

    public function removeProductProperty(ProductProperty $productProperty): static
    {
        if ($this->productProperties->removeElement($productProperty)) {
            // set the owning side to null (unless already changed)
            if ($productProperty->getProduct() === $this) {
                $productProperty->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductVariant>
     */
    public function getProductVariants(): Collection
    {
        return $this->productVariants;
    }

    public function addProductVariant(ProductVariant $productVariant): static
    {
        if (!$this->productVariants->contains($productVariant)) {
            $this->productVariants->add($productVariant);
            $productVariant->setProduct($this);
        }

        return $this;
    }

    public function removeProductVariant(ProductVariant $productVariant): static
    {
        if ($this->productVariants->removeElement($productVariant)) {
            // set the owning side to null (unless already changed)
            if ($productVariant->getProduct() === $this) {
                $productVariant->setProduct(null);
            }
        }

        return $this;
    }
}
