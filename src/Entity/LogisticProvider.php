<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LogisticProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogisticProviderRepository::class)]
class LogisticProvider
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'logisticProvider', targetEntity: Warehouse::class, orphanRemoval: true)]
    private $warehouses;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private $priority = 0;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->warehouses = new ArrayCollection();
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

    /**
     * @return Collection<int, Warehouse>
     */
    public function getWarehouses(): Collection
    {
        return $this->warehouses;
    }

    public function addWarehouse(Warehouse $warehouse): self
    {
        if (!$this->warehouses->contains($warehouse)) {
            $this->warehouses[] = $warehouse;
            $warehouse->setLogisticProvider($this);
        }

        return $this;
    }

    public function removeWarehouse(Warehouse $warehouse): self
    {
        if ($this->warehouses->removeElement($warehouse)) {
            // set the owning side to null (unless already changed)
            if ($warehouse->getLogisticProvider() === $this) {
                $warehouse->setLogisticProvider(null);
            }
        }

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
