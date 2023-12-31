<?php

namespace App\Entity;

use App\Enum\Direction;
use App\Enum\Status;
use App\Repository\TransactionRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\Index(columns: ['method_id'], name: 'transaction__method_id__index')]
#[ORM\Index(columns: ['payer_id'], name: 'transaction__payer_id__index')]
#[ORM\Index(columns: ['currency_id'], name: 'transaction__currency_id__index')]
#[ORM\Index(columns: ['created_at'], name: 'transaction__created_at__index')]
#[ORM\Index(columns: ['status'], name: 'transaction__status__index')]
#[ORM\Index(columns: ['direction'], name: 'transaction__direction__index')]
#[ORM\Index(columns: ['method_id', 'status'], name: 'transaction__method_id__status__index')]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $amount;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private float $user_amount;

    #[ORM\Column(type: Types::SMALLINT, enumType: Status::class)]
    private Status $status = Status::NEW;

    #[ORM\Column(type: Types::SMALLINT, enumType: Direction::class)]
    private Direction $direction;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $payment_details = null;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $created_at;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTime $updated_at;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'id', nullable: false)]
    private Currency $currency;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(name: 'payer_id', referencedColumnName: 'id', nullable: false)]
    private User $payer;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'method_id', referencedColumnName: 'id', nullable: false)]
    private Method $method;

    #[ORM\OneToOne(mappedBy: 'transaction', cascade: ['persist', 'remove'])]
    private ?Ticket $ticket = null;

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

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDirection(): Direction
    {
        return $this->direction;
    }

    public function setDirection(Direction $direction): static
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

    public function getPayer(): User
    {
        return $this->payer;
    }

    public function setPayer(User $payer): static
    {
        $this->payer = $payer;

        return $this;
    }

    public function getMethod(): Method
    {
        return $this->method;
    }

    public function setMethod(Method $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function getUserAmount(): float
    {
        return $this->user_amount;
    }

    public function setUserAmount(float $user_amount): void
    {
        $this->user_amount = $user_amount;
    }

    public function toArray(): array
    {
        return [
            'id'              => $this->getId(),
            'buyer'           => $this->getPayer()->toArray(),
            'method'          => $this->getMethod()->toArray(),
            'amount'          => $this->getAmount(),
            'user_amount'     => $this->getUserAmount(),
            'currency'        => $this->getCurrency()->toArray(),
            'payment_details' => $this->getPaymentDetails(),
            'status'          => $this->getStatus(),
            'direction'       => $this->getDirection(),
            'created_at'      => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at'      => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(Ticket $ticket): static
    {
        // set the owning side of the relation if necessary
        if ($ticket->getTransaction() !== $this) {
            $ticket->setTransaction($this);
        }

        $this->ticket = $ticket;

        return $this;
    }
}
