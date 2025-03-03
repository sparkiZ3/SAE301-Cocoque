<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $orderDate = null;

    #[ORM\Column]
    private ?int $productId = null;

    #[ORM\Column]
    private static ?int $orderNumber = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $customerId = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    private ?Adress $billingAdress = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    private ?Adress $deliveryAdress = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(int $orderNumber): static
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): static
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getBillingAdress(): ?Adress
    {
        return $this->billingAdress;
    }

    public function setBillingAdress(?Adress $billingAdress): static
    {
        $this->billingAdress = $billingAdress;

        return $this;
    }

    public function getDeliveryAdress(): ?Adress
    {
        return $this->deliveryAdress;
    }

    public function setDeliveryAdress(?Adress $deliveryAdress): static
    {
        $this->deliveryAdress = $deliveryAdress;

        return $this;
    }
}
