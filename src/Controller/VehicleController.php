<?php

namespace App\Controller;

use App\Application\Vehicle\CreateVehicleUseCase;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/vehicle')]
class VehicleController extends AbstractController
{

    private $createVehicleUseCase;

    private function __construct(CreateVehicleUseCase $createVehicleUseCase)
    {
        $this->createVehicleUseCase = $createVehicleUseCase;
    }

    #[Route(path: '/', name: 'all_vehicles', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $vehicles = $em->getRepository(Vehicle::class)->findAll();

        return $this->json($vehicles);
    }

    #[Route(path: '/', name: 'create_vehicle', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['brand']) || !isset($data['model']) || !isset($data['dailyRate'])) {
            return $this->json([
                'error' => 'Fields brand, model and dailyRate are mandatory'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $vehicle = $this->createVehicleUseCase->execute(
                $data['brand'],
                $data['model'],
                (float) $data['dailyRate']
            );

            return $this->json([
                'id' => $vehicle->getId(),
                'brand' => $vehicle->getBrand(),
                'model' => $vehicle->getModel(),
                'dailyRate' => $vehicle->getDailyRate(),
                'message' => 'Véhicule créé avec succès'
            ], Response::HTTP_CREATED);

        } catch (AccessDeniedException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error while creating vehicle.',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}