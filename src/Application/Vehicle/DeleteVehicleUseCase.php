<?php

namespace App\Application\Vehicle;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DeleteVehicleUseCase
{
    private $entityManager;
    private $vehicleRepository;

    public function __construct(EntityManagerInterface $entityManager, VehicleRepository $vehicleRepository)
    {
        $this->entityManager = $entityManager;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function execute($vehicleId)
    {
        try {
            $vehicle = $this->vehicleRepository->findOneById($vehicleId);

            if (!$vehicle) {
                throw new Exception("Vehicle not found.");
            }

            $this->entityManager->remove($vehicle);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception("Error while trying to remove vehicle.");
        }
    }
}