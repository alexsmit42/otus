<?php

namespace App\Entity;

use App\Enum\Direction;
use App\Enum\Status;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[ORM\Index(columns: ['method_id'], name: 'transaction__method_id__index')]
#[ORM\Index(columns: ['payer_id'], name: 'transaction__payer_id__index')]
#[ORM\Index(columns: ['currency_id'], name: 'transaction__currency_id__index')]
#[ORM\Index(columns: ['created_at'], name: 'transaction__created_at__index')]
#[ORM\Index(columns: ['status'], name: 'transaction__status__index')]
#[ORM\Index(columns: ['direction'], name: 'transaction__direction__index')]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private float $amount;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $status;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $direction;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $payment_details = null;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $created_at;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTime $updated_at;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'id')]
    private Currency $currency;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\JoinColumn(name: 'payer_id', referencedColumnName: 'id')]
    private ?User $payer = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\JoinColumn(name: 'method_id', referencedColumnName: 'id')]
    private ?Method $method = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDirection(): int
    {
        return $this->direction;
    }

    public function setDirection(int $direction): static
    {
        $this->direction = $direction;

        return $this;
    }

    public function getPaymentDetails(): ?string
    {
        return $this->payment_details;
    }

    public function setPaymentDetails(?string $payment_details): static
    {
        $this->payment_details = $payment_details;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): static
    {
        $this->created_at = new DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTime $updated_at): static
    {
        $this->updated_at = new DateTime();

        return $this;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getPayer(): ?User
    {
        return $this->payer;
    }

    public function setPayer(?User $payer): static
    {
        $this->payer = $payer;

        return $this;
    }

    public function getMethod(): ?Method
    {
        return $this->method;
    }

    public function setMethod(?Method $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id'              => $this->getId(),
            'buyer'           => $this->getPayer()->toArray(),
            'method'          => $this->getMethod()->toArray(),
            'amount'          => $this->getAmount(),
            'currency'        => $this->getCurrency()->toArray(),
            'payment_details' => $this->getPaymentDetails(),
            'status'          => Status::from($this->getStatus()),
            'direction'       => Direction::from($this->getDirection()),
            'created_at'      => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at'      => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
