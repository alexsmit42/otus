<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 3, unique: true)]
    private string $iso;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 6)]
    private float $rate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIso(): string
    {
        return $this->iso;
    }

    public function setIso(string $iso): static
    {
        $this->iso = $iso;

        return $this;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id'   => $this->getId(),
            'iso'  => $this->getIso(),
            'rate' => $this->getRate(),
        ];
    }
}
