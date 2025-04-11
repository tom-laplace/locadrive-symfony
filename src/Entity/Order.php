<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;

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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'orderRef')]
    private Collection $orderItems;

    #[ORM\ManyToOne]
    private ?Insurance $insurance = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?PaymentMethod $paymentMethod = null;

    #[ORM\OneToOne(inversedBy: 'orderRef', cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    public function __construct(Customer $customer)
    {
        $this->orderItems = new ArrayCollection();
        $this->customer = $customer;
        $this->status = "CART";
        $this->creationDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->addOrderRef($this);
        }

        $this->totalAmount = $this->addPriceToTotalAmount($orderItem->getPrice());

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        $this->checkifOrderStatusIsCart();

        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrderRef() === $this) {
                $orderItem->addOrderRef(null);
            }
        }

        $this->totalAmount = $this->removePriceToTotalAmount($orderItem->getPrice());

        return $this;
    }

    public function addPayment(?Payment $payment)
    {
        $this->checkifOrderStatusIsCart();

        $this->payment = $payment;
    }

    public function getInsurance(): ?Insurance
    {
        return $this->insurance;
    }

    public function getPaymentMethod(): ?PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function pay(Payment $payment)
    {
        $this->checkifOrderStatusIsCart();

        if (!$this->getPaymentMethod()) {
            throw new Exception("Please select a payment method before trying to pay for the order.");
        }

        if ($this->getOrderItems()->isEmpty()) {
            throw new Exception("Can not pay for an empty order.");
        }

        $this->payment = $payment;
        $this->status = "PAID";
    }

    public function addInsurance(Insurance $insurance)
    {
        $this->checkifOrderStatusIsCart();

        if ($this->getInsurance() !== null) {
            throw new Exception("Order already assured.");
        }

        $this->totalAmount = $this->addPriceToTotalAmount($this->insurance->getPrice());
        $this->insurance = $insurance;
    }

    public function removeInsurance()
    {
        $this->checkifOrderStatusIsCart();

        if ($this->insurance === null) {
            throw new Exception("This order is not under an insurance.");
        }

        $this->totalAmount = $this->removePriceToTotalAmount($this->insurance->getPrice());
        $this->insurance = null;
    }

    public function addPaymentMethod(?PaymentMethod $paymentMethod)
    {
        $this->checkifOrderStatusIsCart();

        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function addPriceToTotalAmount(float $value)
    {
        $this->checkifOrderStatusIsCart();

        $this->totalAmount += $value;

        return $this->totalAmount;
    }

    public function removePriceToTotalAmount(float $value)
    {
        $this->checkifOrderStatusIsCart();

        $this->totalAmount -= $value;

        return $this->totalAmount;
    }

    private function checkifOrderStatusIsCart()
    {
        if ($this->status !== "CART") {
            throw new Exception("Order is not in cart.");
        }
    }
}
