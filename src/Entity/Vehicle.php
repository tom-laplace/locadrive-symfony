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

        $this->setBrand($brand);
        $this->setModel($model);
        $this->setDailyRate($dailyRate);
        $this->setIsAvailable(true);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    private function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    private function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getDailyRate(): ?float
    {
        return $this->dailyRate;
    }

    private function setDailyRate(float $dailyRate): static
    {
        $this->dailyRate = $dailyRate;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    private function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }
}
