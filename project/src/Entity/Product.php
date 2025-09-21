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
     * @var Collection<int, CartItem>
     */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'product', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $cartItems;

    #[ORM\Column]
    private int $popularityIndex = 0;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
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
     * @return Collection<int, CartItem>
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function addCartItem(CartItem $cartItem): static
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems->add($cartItem);
            $cartItem->setProduct($this);
        }

        return $this;
    }

    public function removeCartItem(CartItem $cartItem): static
    {
        if ($this->cartItems->removeElement($cartItem)) {
            // set the owning side to null (unless already changed)
            if ($cartItem->getProduct() === $this) {
                $cartItem->setProduct(null);
            }
        }

        return $this;
    }

    public function getPopularityIndex(): int
    {
        return $this->popularityIndex;
    }

    public function setPopularityIndex(int $popularityIndex): static
    {
        $this->popularityIndex = $popularityIndex;

        return $this;
    }
}
