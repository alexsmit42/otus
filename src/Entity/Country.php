<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40, unique: true)]
    private string $name;

    #[ORM\ManyToMany(targetEntity: Method::class, inversedBy: 'countries')]
    private Collection $methods;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: Product::class)]
    private Collection $products;

    public function __construct()
    {
        $this->methods  = new ArrayCollection();
        $this->products = new ArrayCollection();
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

    /**
     * @return Collection<int, Method>
     */
    public function getMethods(): Collection
    {
        return $this->methods;
    }

    public function addMethod(Method $method): static
    {
        if (!$this->methods->contains($method)) {
            $this->methods->add($method);
        }

        return $this;
    }

    public function removeMethod(Method $method): static
    {
        $this->methods->removeElement($method);

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function toArray(): array
    {
        return [
            'id'   => $this->getId(),
            'name' => $this->getName(),
        ];
    }
}
