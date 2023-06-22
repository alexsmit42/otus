<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`user`')]
#[ORM\Index(columns: ['country_id'], name: 'user__country_id__index')]
#[ORM\Index(columns: ['currency_id'], name: 'user__currency_id__index')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $login = null;

    #[ORM\Column(options: [
        'default' => 0,
    ])]
    private ?float $balance = 0;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'id')]
    private ?Currency $currency = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
    private ?Country $country = null;

    #[ORM\OneToMany(mappedBy: 'payer', targetEntity: Transaction::class)]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: 'buyer', targetEntity: Purchase::class)]
    private Collection $purchases;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->purchases    = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): static
    {
        $this->balance = $balance;

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
            $transaction->setPayer($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getPayer() === $this) {
                $transaction->setPayer(null);
            }
        }

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
            $purchase->setBuyer($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): static
    {
        if ($this->purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getBuyer() === $this) {
                $purchase->setBuyer(null);
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->getId(),
            'login'    => $this->getLogin(),
            'balance'  => $this->getBalance(),
            'country'  => $this->getCountry()->toArray(),
            'currency' => $this->getCurrency()->toArray(),
        ];
    }
}
