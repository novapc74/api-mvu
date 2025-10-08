<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SizeRepository;
use JetBrains\PhpStorm\ArrayShape;

#[ORM\Entity(repositoryClass: SizeRepository::class)]
class Size
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5)]
    private ?string $size = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    #[ArrayShape([
        'XXS' => 'string',
        'XS' => 'string',
        'S' => 'string',
        'M' => 'string',
        'L' => 'string',
        'XL' => 'string',
        'XXL' => 'string',
    ])]
    public static function getAvailableSizes(bool $isFlip = false): array
    {
        $sizes = [
            'XXS' => 'XXS',
            'XS' => 'XS',
            'S' => 'S',
            'M' => 'M',
            'L' => 'L',
            'XL' => 'XL',
            'XXL' => 'XXL',
        ];

        return $isFlip ? array_flip($sizes) : $sizes;
    }
}
