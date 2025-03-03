<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $productName = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $unitPrice = null;

    #[ORM\Column(length: 255)]
    private ?string $imageURL = null;

    #[ORM\Column(length: 7)]
    private ?string $color = null;

    #[ORM\Column(length: 255)]
    private ?string $modelName = null;

    #[ORM\Column(length: 255)]
    private ?string $labels = null;

    #[ORM\Column]
    private ?int $collectionId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setProductKey(int $productKey): static
    {
        $this->productKey = $productKey;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): static
    {
        $this->productName = $productName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUnitPrice(): ?int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getImageURL(): ?string
    {
        return $this->imageURL;
    }

    public function setImageURL(string $imageURL): static
    {
        $this->imageURL = $imageURL;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getModelName(): ?string
    {
        return $this->modelName;
    }

    public function setModelName(string $modelName): static
    {
        $this->modelName = $modelName;

        return $this;
    }

    public function getLabels(): ?string
    {
        return $this->labels;
    }

    public function setLabels(string $labels): static
    {
        $this->labels = $labels;

        return $this;
    }

    public function getCollectionId(): ?int
    {
        return $this->collectionId;
    }

    public function setCollectionId(int $collectionId): static
    {
        $this->collectionId = $collectionId;

        return $this;
    }
}
