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

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column]
    private ?float $totalAmount = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\ManyToOne]
    private ?Insurance $insuranceId = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?PaymentMethod $paymentMethodId = null;

    #[ORM\OneToOne(inversedBy: 'relatedOrder', cascade: ['persist', 'remove'])]
    private ?Payment $paymentId = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Customer $customer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getInsuranceId(): ?Insurance
    {
        return $this->insuranceId;
    }

    public function setInsuranceId(?Insurance $insuranceId): static
    {
        $this->insuranceId = $insuranceId;

        return $this;
    }

    public function getPaymentMethodId(): ?PaymentMethod
    {
        return $this->paymentMethodId;
    }

    public function setPaymentMethodId(?PaymentMethod $paymentMethodId): static
    {
        $this->paymentMethodId = $paymentMethodId;

        return $this;
    }

    public function getPaymentId(): ?Payment
    {
        return $this->paymentId;
    }

    public function setPaymentId(?Payment $paymentId): static
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }
}
