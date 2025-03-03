<?php

namespace App\Entity;

use App\Repository\CollectionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollectionsRepository::class)]
class Collections
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $collectionName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCollectionName(): ?string
    {
        return $this->collectionName;
    }

    public function setCollectionName(string $collectionName): static
    {
        $this->collectionName = $collectionName;

        return $this;
    }
}
