<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use App\Repository\ColorRepository;

#[ORM\Entity(repositoryClass: ColorRepository::class)]
class Color
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private string $name = 'white';

    #[ORM\Column(length: 7)]
    private string $hexCode = '#FFFFFF';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getHexCode(): string
    {
        return $this->hexCode;
    }

    public function setHexCode(string $hexCode): static
    {
        $this->hexCode = $hexCode;

        return $this;
    }

    #[ArrayShape([
        'red' => 'string',
        'orange' => 'string',
        'yellow' => 'string',
        'green' => 'string',
        'blue' => 'string',
        'midnight_blue' => 'string',
        'purple' => 'string',
        'white' => 'string',
        'black' => 'string',
    ])]
    public static function getAvailableColors(bool $isFlip = false): array
    {
        $colorData = [
            'red' => '#FF0000',
            'orange' => '#FFA500',
            'yellow' => '#FFFF00',
            'green' => '#008000',
            'blue' => '#0000FF',
            'midnight_blue' => '#191970',
            'purple' => '#800080',
            'white' => '#FFFFFF',
            'black' => '#000000',
        ];

        return $isFlip ? array_flip($colorData) : $colorData;
    }
}
