<?php

namespace App\Entity;

use App\Repository\MethodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MethodRepository::class)]
class Method
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?float $min_limit = null;

    #[ORM\Column(nullable: true)]
    private ?float $max_limit = null;

    #[ORM\ManyToMany(targetEntity: Country::class, mappedBy: 'methods')]
    private Collection $countries;

    #[ORM\OneToMany(mappedBy: 'method', targetEntity: Transaction::class)]
    private Collection $transactions;

    public function __construct()
    {
        $this->countries = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

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
            $country->addMethod($this);
        }

        return $this;
    }

    public function removeCountry(Country $country): static
    {
        if ($this->countries->removeElement($country)) {
            $country->removeMethod($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setMethod($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getMethod() === $this) {
                $transaction->setMethod(null);
            }
        }

        return $this;
    }
}
