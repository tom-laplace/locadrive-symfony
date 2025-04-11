<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $brand = null;

    #[ORM\Column(length: 100)]
    private ?string $model = null;

    #[ORM\Column]
    private ?float $dailyRate = null;

    #[ORM\Column]
    private ?bool $isAvailable = null;

    public function __construct($brand, $model, $dailyRate)
    {
        if (!is_string($brand) || !is_string($model)) {
            throw new \InvalidArgumentException("Bad arguments for brand or model.");
        }

        if ($dailyRate < 1) {
            throw new \InvalidArgumentException("Daily rate can't be inferior to 1.");
        }

        $this->brand = $brand;
        $this->model = $model;
        $this->dailyRate = $dailyRate;
        $this->isAvailable = true;
    }

    public function update($brand, $model, $dailyRate, $isAvailable)
    {
        if (!is_string($brand) || !is_string($model)) {
            throw new \InvalidArgumentException("Bad arguments for brand or model.");
        }

        if ($dailyRate < 1) {
            throw new \InvalidArgumentException("Daily rate can't be inferior to 1.");
        }

        $this->brand = $brand;
        $this->model = $model;
        $this->dailyRate = $dailyRate;
        $this->isAvailable = $isAvailable;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function getDailyRate(): ?float
    {
        return $this->dailyRate;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }
}
