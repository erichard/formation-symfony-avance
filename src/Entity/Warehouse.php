<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\WarehouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WarehouseRepository::class)]
class Warehouse
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: LogisticProvider::class, inversedBy: 'warehouses')]
    #[ORM\JoinColumn(nullable: false)]
    private LogisticProvider $logisticProvider;

    #[ORM\OneToMany(mappedBy: 'warehouse', targetEntity: Stock::class, orphanRemoval: true)]
    private $stocks;

    #[ORM\Column(type: 'string', length: 20, nullable: true, unique: true)]
    private $reference;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private $priority = 0;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->stocks = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLogisticProvider(): ?LogisticProvider
    {
        return $this->logisticProvider;
    }

    public function setLogisticProvider(?LogisticProvider $logisticProvider): self
    {
        $this->logisticProvider = $logisticProvider;

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks[] = $stock;
            $stock->setWarehouse($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getWarehouse() === $this) {
                $stock->setWarehouse(null);
            }
        }

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}
