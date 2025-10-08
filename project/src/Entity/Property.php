<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PropertyRepository;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
class Property
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Measure::class, cascade: ['persist'], inversedBy: 'property')]
    private ?Measure $measure = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMeasure(): ?Measure
    {
        return $this->measure;
    }

    public function setMeasure(?Measure $measure): static
    {
        $this->measure = $measure;

        return $this;
    }
}
