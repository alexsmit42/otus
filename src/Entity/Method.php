<?php

namespace App\Entity;

use App\Repository\MethodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;

#[ORM\Entity(repositoryClass: MethodRepository::class)]
class Method
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private string $name;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $min_limit = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $max_limit = null;

    #[ORM\ManyToMany(targetEntity: Country::class)]
    #[JoinColumn(name: 'method_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'country_id', referencedColumnName: 'id')]
    private Collection $countries;

    public function __construct()
    {
        $this->countries = new ArrayCollection();
    }

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

    public function getMinLimit(): ?float
    {
        return $this->min_limit;
    }

    public function setMinLimit(?float $min_limit): static
    {
        $this->min_limit = $min_limit;

        return $this;
    }

    public function getMaxLimit(): ?float
    {
        return $this->max_limit;
    }

    public function setMaxLimit(?float $max_limit): static
    {
        $this->max_limit = $max_limit;

        return $this;
    }

    /**
     * @return Collection<int, Country>
     */
    public function getCountries(): Collection
    {
        return $this->countries;
    }

    public function addCountry(Country $country): static
    {
        if (!$this->countries->contains($country)) {
            $this->countries->add($country);
        }

        return $this;
    }

    public function removeCountry(Country $country): static
    {
        $this->countries->removeElement($country);

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id'        => $this->getId(),
            'name'      => $this->getName(),
            'min_limit' => $this->getMinLimit(),
            'max_limit' => $this->getMaxLimit(),
        ];
    }
}
