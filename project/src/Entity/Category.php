<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\IdentifierTrait;
use App\Repository\CategoryRepository;
use App\Entity\Trait\UuidGeneratorTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'IDX_CATEGORY_SLUG', columns: ['slug'])]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    use UuidGeneratorTrait;
    use IdentifierTrait;

    #[ORM\ManyToOne(targetEntity: self::class, cascade: ['persist', 'remove'], inversedBy: 'categories')]
    private ?self $category = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'category', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $categories;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $products;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public function getCategory(): ?self
    {
        return $this->category;
    }

    public function setCategory(?self $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(self $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setCategory($this);
        }

        return $this;
    }

    public function removeCategory(self $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getCategory() === $this) {
                $category->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }
}
