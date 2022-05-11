<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Countries;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 15)]
    private $id;

    #[ORM\Column(type: 'string', length: 10)]
    private $reference;

    #[ORM\Column(type: 'integer')]
    private $colorCode;

    #[ORM\Column(type: 'string', length: 100)]
    private $title;

    #[ORM\Column(type: 'string', length: 150)]
    private $color;

    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(name: 'brand', nullable: false)]
    private $brand;

    #[ORM\Column(type: 'string', length: 3)]
    private $saison;

    #[ORM\Column(type: 'json')]
    private $saisonsCommerciales;

    #[ORM\ManyToOne(targetEntity: Forme::class)]
    #[ORM\JoinColumn(name: 'forme', nullable: false)]
    private $forme;

    #[ORM\ManyToOne(targetEntity: Fabrication::class)]
    #[ORM\JoinColumn(name: 'fabrication')]
    private $fabrication;

    #[ORM\Column(type: 'json', nullable: true)]
    private $semelles;

    #[ORM\Column(type: 'json', nullable: true)]
    private $tige = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private $doublure = [];

    #[ORM\Column(type: 'string', length: 2)]
    private $madeIn;

    #[ORM\ManyToOne(targetEntity: Fermeture::class)]
    #[ORM\JoinColumn(name: 'fermeture', nullable: true)]
    private $fermeture;

    #[ORM\ManyToOne(targetEntity: Semelle::class)]
    #[ORM\JoinColumn(name: 'semelle')]
    private $semelle;

    #[ORM\ManyToOne(targetEntity: Ligne::class)]
    #[ORM\JoinColumn(name: 'ligne')]
    private $ligne;

    #[ORM\ManyToOne(targetEntity: Genre::class)]
    #[ORM\JoinColumn(name: 'genre')]
    private $genre;

    #[ORM\Column(type: 'decimal', precision: 3, scale: 1, nullable: true)]
    private $hauteurTalon;

    #[ORM\Column(type: 'string', length: 8, nullable: true)]
    private $pictogram;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private $typeTalon;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $hauteurTige;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $hauteurPlateforme;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Product::class, orphanRemoval: true)]
    #[ORM\OrderBy(['size' => 'ASC'])]
    private $products;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private $quantityInStock = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $minPrixVente;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $minPrixAchat;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->brand->getName() . ' - ' . $this->title;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getSaison(): ?string
    {
        return $this->saison;
    }

    public function setSaison(string $saison): self
    {
        $this->saison = $saison;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getSaisonsCommerciales(): ?array
    {
        return $this->saisonsCommerciales;
    }

    public function setSaisonsCommerciales(array $saisonsCommerciales): self
    {
        $this->saisonsCommerciales = $saisonsCommerciales;

        return $this;
    }

    public function getForme(): ?Forme
    {
        return $this->forme;
    }

    public function setForme(Forme $forme): self
    {
        $this->forme = $forme;

        return $this;
    }

    public function getFabrication(): ?Fabrication
    {
        return $this->fabrication;
    }

    public function setFabrication(?Fabrication $fabrication): self
    {
        $this->fabrication = $fabrication;

        return $this;
    }

    public function getSemelles(): ?array
    {
        return $this->semelles;
    }

    public function setSemelles(?array $semelles): self
    {
        $this->semelles = $semelles;

        return $this;
    }

    public function getTige(): ?array
    {
        return $this->tige;
    }

    public function setTige(?array $tige): self
    {
        $this->tige = $tige;

        return $this;
    }

    public function getDoublure(): ?array
    {
        return $this->doublure;
    }

    public function setDoublure(?array $doublure): self
    {
        $this->doublure = $doublure;

        return $this;
    }

    public function getMadeIn(): ?string
    {
        return $this->madeIn;
    }

    public function getMadeInCountry(): ?string
    {
        return Countries::getName($this->madeIn);
    }

    public function setMadeIn(string $madeIn): self
    {
        $this->madeIn = $madeIn;

        return $this;
    }

    public function getFermeture(): ?Fermeture
    {
        return $this->fermeture;
    }

    public function setFermeture(?Fermeture $fermeture): self
    {
        $this->fermeture = $fermeture;

        return $this;
    }

    public function getSemelle(): ?Semelle
    {
        return $this->semelle;
    }

    public function setSemelle(?Semelle $Semelle): self
    {
        $this->semelle = $semelle;

        return $this;
    }

    public function getLigne(): ?Ligne
    {
        return $this->ligne;
    }

    public function setLigne(?Ligne $ligne): self
    {
        $this->ligne = $ligne;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getHauteurTalon(): ?string
    {
        return $this->hauteurTalon;
    }

    public function setHauteurTalon(?string $hauteurTalon): self
    {
        $this->hauteurTalon = $hauteurTalon;

        return $this;
    }

    public function getPictogram(): ?string
    {
        return $this->pictogram;
    }

    public function setPictogram(?string $pictogram): self
    {
        $this->pictogram = $pictogram;

        return $this;
    }

    public function getTypeTalon(): ?string
    {
        return $this->typeTalon;
    }

    public function setTypeTalon(?string $typeTalon): self
    {
        $this->typeTalon = $typeTalon;

        return $this;
    }

    public function getHauteurTige(): ?int
    {
        return $this->hauteurTige;
    }

    public function setHauteurTige(int $hauteurTige): self
    {
        $this->hauteurTige = $hauteurTige;

        return $this;
    }

    public function getHauteurPlateforme(): ?int
    {
        return $this->hauteurPlateforme;
    }

    public function setHauteurPlateforme(?int $hauteurPlateforme): self
    {
        $this->hauteurPlateforme = $hauteurPlateforme;

        return $this;
    }

    /**
     * @return Collection<int, Products>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addSize(Product $size): self
    {
        if (!$this->products->contains($size)) {
            $this->products[] = $size;
            $size->setArticle($this);
        }

        return $this;
    }

    public function removeSize(Product $size): self
    {
        if ($this->products->removeElement($size)) {
            // set the owning side to null (unless already changed)
            if ($size->getArticle() === $this) {
                $size->setArticle(null);
            }
        }

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

    public function getColorCode(): ?int
    {
        return $this->colorCode;
    }

    public function setColorCode(int $colorCode): self
    {
        $this->colorCode = $colorCode;

        return $this;
    }

    public function getMinPrixVenteActualized(): ?int
    {
        $minPrixVente = null;

        foreach($this->products as $product) {
            if ($product->getPrixVente() > $minPrixVente) {
                $minPrixVente = $product->getPrixVente();
            }
        }

        return $minPrixVente;
    }

    public function getMinPrixVente(): ?int
    {
        return $this->minPrixVente;
    }

    public function setMinPrixVente(?int $minPrixVente): self
    {
        $this->minPrixVente = $minPrixVente;

        return $this;
    }

    public function getMinPrixAchat(): ?int
    {
        return $this->minPrixAchat;
    }

    public function setMinPrixAchat(?int $minPrixAchat): self
    {
        $this->minPrixAchat = $minPrixAchat;

        return $this;
    }
}
