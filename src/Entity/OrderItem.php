<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderRef = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicle $vehicle = null;

    public function __construct(Order $orderRef, Vehicle $vehicle, DateTime $startDate, DateTime $endDate, float $price)
    {
        $today = new DateTime();

        if ($startDate < $today) {
            throw new InvalidArgumentException('La date de début doit être ultérieure à aujourd\'hui');
        }

        if ($endDate <= $startDate) {
            throw new InvalidArgumentException('La date de fin doit être ultérieure à la date de début');
        }

        $this->orderRef = $orderRef;
        $this->vehicle = $vehicle;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->price = $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getOrderRef(): ?Order
    {
        return $this->orderRef;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }
}
