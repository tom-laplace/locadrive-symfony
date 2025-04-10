<?php

namespace App\Application\Vehicle;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CreateVehicleUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function execute(string $brand, string $model, float $dailyRate)
    {

        $vehicle = new Vehicle($brand, $model, $dailyRate);

        try {
            $this->entityManager->persist($vehicle);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception("Error while creating vehicle. Please try again.");
        }

        return $vehicle;
    }
}