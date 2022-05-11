<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 13)]
    private $id;

    #[ORM\Column(type: 'string', length: 5)]
    private $size;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $weight;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private $article;

    #[ORM\OneToMany(mappedBy: 'ean', targetEntity: Stock::class, orphanRemoval: true)]
    private $stocks;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private $quantityInStock = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $prixVente;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $prixAchat;

    public function __construct()
    {
        $this->stocks = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->article->getTitle(). ' - '.$this->size;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getQuantityInStock(): ?int
    {
        return $this->quantityInStock;
    }

    public function setQuantityInStock(int $quantityInStock): self
    {
        $this->quantityInStock = $quantityInStock;

        return $this;
    }

    public function getPrixVente(): ?int
    {
        return $this->prixVente;
    }

    public function getPrixVenteFormatted(): ?string
    {
        if (null === $this->prixVente) {
            return '';
        }

        return number_format($this->prixVente / 100, 2, ',', ' '). '&nbsp€';
    }

    public function setPrixVente(?int $prixVente): self
    {
        $this->prixVente = $prixVente;

        return $this;
    }

    public function getPrixAchat(): ?int
    {
        return $this->prixAchat;
    }

    public function getPrixAchatFormatted(): ?string
    {
        if (null === $this->prixAchat) {
            return '';
        }

        return number_format($this->prixAchat / 100, 2, ',', ' '). '&nbsp€';
    }

    public function setPrixAchat(?int $prixAchat): self
    {
        $this->prixAchat = $prixAchat;

        return $this;
    }
}
