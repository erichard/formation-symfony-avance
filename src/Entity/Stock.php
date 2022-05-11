<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
#[ORM\UniqueConstraint(columns: ['ean', 'warehouse_id'])]
#[ORM\Index(fields: ['ean'])]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Warehouse::class, inversedBy: 'stocks')]
    #[ORM\JoinColumn(nullable: false)]
    private $warehouse;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'stocks')]
    #[ORM\JoinColumn(nullable: false, name: 'ean')]
    private $ean;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private $quantityOnHand;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private $quantityScheduled;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private $quantityAvailable;

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    public function getEan(): ?Product
    {
        return $this->ean;
    }

    public function setEan(Product $ean): self
    {
        $this->ean = $ean;

        return $this;
    }

    public function getQuantityOnHand(): ?int
    {
        return $this->quantityOnHand;
    }

    public function setQuantityOnHand(int $quantityOnHand): self
    {
        $this->quantityOnHand = $quantityOnHand;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getQuantityScheduled(): ?int
    {
        return $this->quantityScheduled;
    }

    public function setQuantityScheduled(int $quantityScheduled): self
    {
        $this->quantityScheduled = $quantityScheduled;

        return $this;
    }

    public function getQuantityAvailable(): ?int
    {
        return $this->quantityAvailable;
    }

    public function setQuantityAvailable(int $quantityAvailable): self
    {
        $this->quantityAvailable = $quantityAvailable;

        return $this;
    }
}
