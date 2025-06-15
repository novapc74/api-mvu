<?php

namespace App\Entity\Trait;

use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Validator\Constraints as Assert;

trait UuidGeneratorTrait
{
    #[ORM\Id]
    #[Assert\Uuid]
    #[ORM\Column(type: UuidType::NAME, unique: true, nullable: false)]
    private ?Uuid $id = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): self
    {
        $this->id = $id;

        return $this;
    }

    #[ORM\PrePersist]
    public function setUuidValue(): void
    {
        if ($this->id === null) {
            $this->id = Uuid::v4();
        }
    }
}
