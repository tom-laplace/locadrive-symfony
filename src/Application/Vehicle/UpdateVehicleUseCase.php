<?php

namespace App\Application\Vehicle;

use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class UpdateVehicleUseCase
{
    private EntityManagerInterface $entityManager;
    private VehicleRepository $vehicleRepository;

    public function __construct(EntityManagerInterface $entityManager, VehicleRepository $vehicleRepository)
    {
        $this->entityManager = $entityManager;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function execute($vehicleId, $brand, $model, $dailyRate, $isAvailable)
    {
        try {
            $vehicle = $this->vehicleRepository->findOneById($vehicleId);

            if (!$vehicle) {
                throw new Exception("Vehicle not found.");
            }

            try {
                $vehicle->update($brand, $model, $dailyRate, $isAvailable);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }

            $this->entityManager->persist($vehicle);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception("Error while trying to remove vehicle.");
        }

        return $vehicle;
    }
}