<?php

namespace App\Entity;

use App\Repository\AdressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdressRepository::class)]
class Adress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[ORM\Column]
    private ?int $zipcode = null;

    #[ORM\Column(length: 255)]
    private ?string $streetAdress = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $numBatiment = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $appartementNumber = null;

    #[ORM\Column]
    private ?int $userId = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $phoneNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getZipcode(): ?int
    {
        return $this->zipcode;
    }

    public function setZipcode(int $zipcode): static
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getStreetAdress(): ?string
    {
        return $this->streetAdress;
    }

    public function setStreetAdress(string $streetAdress): static
    {
        $this->streetAdress = $streetAdress;

        return $this;
    }

    public function getNumBatiment(): ?string
    {
        return $this->numBatiment;
    }

    public function setNumBatiment(?string $numBatiment): static
    {
        $this->numBatiment = $numBatiment;

        return $this;
    }

    public function getAppartementNumber(): ?string
    {
        return $this->appartementNumber;
    }

    public function setAppartementNumber(?string $appartementNumber): static
    {
        $this->appartementNumber = $appartementNumber;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }
}
