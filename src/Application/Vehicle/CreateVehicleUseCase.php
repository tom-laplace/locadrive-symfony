<?php

namespace App\Application\Vehicle;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class CreateVehicleUseCase
{
    private $entityManager;
    private $security;


    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }


    public function execute(string $brand, string $model, float $dailyRate)
    {
        $user = $this->security->getUser();
        if (!$user instanceof Administrator) {
            throw new AccessDeniedException('Only administrator can create vehicles.');
        }

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