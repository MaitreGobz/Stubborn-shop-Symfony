<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    /**
     * Identifiant unique du produit.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom du sweat-shirt.
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * Prix du sweat-shirt.
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $price = null;

    /**
     * Nom du fichier image.
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * Produit mis en avant.
     */
    #[ORM\Column]
    private ?bool $featured = false;

    /**
     * Stcko disponible en XS.
     */
    #[ORM\Column]
    private ?int $stockXs = 2;
    
    /**
     * Stcko disponible en S.
     */
    #[ORM\Column]
    private ?int $stockS = 2;

    /**
     * Stcko disponible en M.
     */
    #[ORM\Column]
    private ?int $stockM = 2;

    /**
     * Stcko disponible en L.
     */
    #[ORM\Column]
    private ?int $stockL = 2;

    /**
     * Stcko disponible en XL.
     */
    #[ORM\Column]
    private ?int $stockXl = 2;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): static
    {
        $this->featured = $featured;

        return $this;
    }

    public function getStockXs(): ?int
    {
        return $this->stockXs;
    }

    public function setStockXs(int $stockXs): static
    {
        $this->stockXs = $stockXs;

        return $this;
    }

    public function getStockS(): ?int
    {
        return $this->stockS;
    }

    public function setStockS(int $stockS): static
    {
        $this->stockS = $stockS;

        return $this;
    }

    public function getStockM(): ?int
    {
        return $this->stockM;
    }

    public function setStockM(int $stockM): static
    {
        $this->stockM = $stockM;

        return $this;
    }

    public function getStockL(): ?int
    {
        return $this->stockL;
    }

    public function setStockL(int $stockL): static
    {
        $this->stockL = $stockL;

        return $this;
    }

    public function getStockXl(): ?int
    {
        return $this->stockXl;
    }

    public function setStockXl(int $stockXl): static
    {
        $this->stockXl = $stockXl;

        return $this;
    }
}
