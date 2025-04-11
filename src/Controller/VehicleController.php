<?php

namespace App\Controller;

use App\Application\Vehicle\CreateVehicleUseCase;
use App\Application\Vehicle\DeleteVehicleUseCase;
use App\Application\Vehicle\UpdateVehicleUseCase;
use App\Entity\Administrator;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/vehicle')]
class VehicleController extends AbstractController
{

    private $createVehicleUseCase;
    private $updateVehicleUseCase;
    private $deleteVehicleUseCase;
    public function __construct(CreateVehicleUseCase $createVehicleUseCase, UpdateVehicleUseCase $updateVehicleUseCase, DeleteVehicleUseCase $deleteVehicleUseCase)
    {
        $this->createVehicleUseCase = $createVehicleUseCase;
        $this->updateVehicleUseCase = $updateVehicleUseCase;
        $this->deleteVehicleUseCase = $deleteVehicleUseCase;
    }

    #[Route(path: '/', name: 'all_vehicles', methods: 'GET')]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $vehicles = $em->getRepository(Vehicle::class)->findAll();

        return $this->json($vehicles);
    }

    #[Route(path: '/', name: 'create_vehicle', methods: 'POST')]
    public function create(Request $request): JsonResponse
    {

        $user = $this->getUser();
        if (!$user instanceof Administrator) {
            throw new AccessDeniedException('Only administrator can create vehicles.');
        }

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

    #[Route(path: '/{id}', name: 'update_vehicle', methods: 'PUT')]
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof Administrator) {
            throw new AccessDeniedException('Only administrator can create vehicles.');
        }

        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['brand']) || !isset($data['model']) || !isset($data['dailyRate']) || !isset($data['isAvailable'])) {
                return $this->json([
                    'error' => 'Fields brand, model, dailyRate and isAvailable are mandatory'
                ], Response::HTTP_BAD_REQUEST);
            }

            $vehicle = $this->updateVehicleUseCase->execute($id, $data['brand'], $data['model'], $data['dailyRate'], $data['isAvailable']);

            return $this->json([
                'id' => $vehicle->getId(),
                'brand' => $vehicle->getBrand(),
                'model' => $vehicle->getModel(),
                'dailyRate' => $vehicle->getDailyRate(),
                'message' => 'Véhicule modifié avec succès'
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

    #[Route(path: '/{id}', name: 'delete_vehicle', methods: 'DELETE')]
    public function delete(Request $request, int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof Administrator) {
            throw new AccessDeniedException('Only administrator can create vehicles.');
        }

        try {
            $this->deleteVehicleUseCase->execute($id);

            return $this->json([], Response::HTTP_NO_CONTENT);
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