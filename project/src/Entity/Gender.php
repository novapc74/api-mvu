<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use App\Repository\GenderRepository;

#[ORM\Entity(repositoryClass: GenderRepository::class)]
class Gender
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 7)]
    private ?string $gender = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    #[ArrayShape([
        'Детские' => "string",
        'Женские' => "string",
        'Мужские' => "string",
    ])]
    public static function getAvailableGenders(bool $is_flip = false): array
    {
        $genders = [
            'Детские' => "Детские",
            'Женские' => "Женские",
            'Мужские' => "Мужские",
        ];

        return $is_flip ? array_flip($genders) : $genders;
    }
}
