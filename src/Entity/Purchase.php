<?php

namespace App\Entity;

use App\Enum\Status;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[ORM\Index(columns: ['buyer_id'], name: 'purchase__buyer_id__index')]
#[ORM\Index(columns: ['created_at'], name: 'purchase__created_at__index')]
#[ORM\Index(columns: ['status'], name: 'purchase__status__index')]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(name: 'buyer_id', referencedColumnName: 'id')]
    private ?User $buyer = null;

    #[ORM\ManyToMany(targetEntity: Product::class)]
    #[JoinColumn(name: 'purchase_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'product_id', referencedColumnName: 'id')]
    private Collection $products;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $created_at;

    #[ORM\Column(type: Types::SMALLINT, enumType: Status::class)]
    private Status $status;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuyer(): User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): static
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        $this->products->removeElement($product);

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

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

    public function toArray(): array
    {
        return [
            'id'         => $this->getId(),
            'buyer'      => $this->getBuyer(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'products'   => array_map(
                static fn(Product $product) => $product->toArray(),
                $this->getProducts()->toArray()
            ),
        ];
    }
}
