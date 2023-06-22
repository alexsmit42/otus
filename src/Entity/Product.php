<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Index(columns: ['currency_id'], name: 'product__currency_id__index')]
#[ORM\Index(columns: ['country_id'], name: 'product__country_id__index')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'id')]
    private ?Currency $currency = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
    private ?Country $country = null;

    #[ORM\ManyToMany(targetEntity: Purchase::class, mappedBy: 'products')]
    private Collection $purchases;

    public function __construct()
    {
        $this->purchases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): static
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->addProduct($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): static
    {
        if ($this->purchases->removeElement($purchase)) {
            $purchase->removeProduct($this);
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->getId(),
            'title'    => $this->getTitle(),
            'amount'   => $this->getAmount(),
            'currency' => $this->getCurrency()->toArray(),
            'country'  => $this->getCountry()->toArray(),
        ];
    }
}
