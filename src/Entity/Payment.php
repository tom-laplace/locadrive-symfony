<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $paymentDate = null;

    #[ORM\Column(length: 255)]
    private ?string $transactionId = null;

    #[ORM\OneToOne(mappedBy: 'payment', cascade: ['persist', 'remove'])]
    private ?Order $orderRef = null;

    public function __construct(float $amount)
    {
        $this->amount = $amount;
        $this->paymentDate = new \DateTime();
        $this->transactionId = $this->generateTransactionId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function getOrderRef(): ?Order
    {
        return $this->orderRef;
    }

    public function setOrderRef(?Order $orderRef): static
    {
        // unset the owning side of the relation if necessary
        if ($orderRef === null && $this->orderRef !== null) {
            $this->orderRef->addPayment(null);
        }

        // set the owning side of the relation if necessary
        if ($orderRef !== null && $orderRef->getPayment() !== $this) {
            $orderRef->addPayment($this);
        }

        $this->orderRef = $orderRef;

        return $this;
    }

    private function generateTransactionId(): string
    {
        return 'TR-' . uniqid() . '-' . time();
    }

}
