<?php

namespace App\Controller;

use App\Application\Order\AddInsuranceToOrderUseCase;
use App\Application\Order\AddVehicleToOrderUseCase;
use App\Entity\Customer;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("api/customer/order")]
class OrderController extends AbstractController
{
    private $addVehicleToOrderUseCase;
    private $addInsuranceToOrderUseCase;

    public function __construct(AddVehicleToOrderUseCase $addVehicleToOrderUseCase, AddInsuranceToOrderUseCase $addInsuranceToOrderUseCase)
    {
        $this->addVehicleToOrderUseCase = $addVehicleToOrderUseCase;
        $this->addInsuranceToOrderUseCase = $addInsuranceToOrderUseCase;
    }

    #[Route('/add-vehicle', name: 'add_vehicle_to_order', methods: ['POST'])]
    public function addVehicleToOrder(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof Customer) {
            throw new AccessDeniedException('Only customer can update orders.');
        }

        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['vehicleId']) || !isset($data['startDate']) || !isset($data['endDate'])) {
                return $this->json([
                    'error' => 'All fields are mandatory.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $startDate = new DateTime($data['startDate']);
            $endDate = new DateTime($data['endDate']);
            $orderId = $data['orderId'] ?? null;

            $order = $this->addVehicleToOrderUseCase->execute(
                $user,
                (int) $data['vehicleId'],
                $startDate,
                $endDate,
                $orderId
            );

            $orderItems = [];
            foreach ($order->getOrderItems() as $item) {
                $orderItems[] = [
                    'id' => $item->getId(),
                    'vehicle' => [
                        'id' => $item->getVehicle()->getId(),
                        'brand' => $item->getVehicle()->getBrand(),
                        'model' => $item->getVehicle()->getModel(),
                        'dailyRate' => $item->getVehicle()->getDailyRate()
                    ],
                    'startDate' => $item->getStartDate()->format('Y-m-d'),
                    'endDate' => $item->getEndDate()->format('Y-m-d'),
                    'price' => $item->getPrice()
                ];
            }

            return $this->json([
                'id' => $order->getId(),
                'status' => $order->getStatus(),
                'totalAmount' => $order->getTotalAmount(),
                'creationDate' => $order->getCreationDate()->format('Y-m-d H:i:s'),
                'items' => $orderItems,
                'customer' => [
                    'id' => $order->getCustomer()->getId(),
                    'firstName' => $order->getCustomer()->getFirstName(),
                    'lastName' => $order->getCustomer()->getLastName()
                ],
                'insurance' => $order->getInsurance() ? [
                    'id' => $order->getInsurance()->getId(),
                    'price' => $order->getInsurance()->getPrice(),
                    'description' => $order->getInsurance()->getDescription()
                ] : null,
                'message' => 'Insurance added to the order.'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], $e instanceof AccessDeniedException ? Response::HTTP_FORBIDDEN : Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route(path: '/{id}/add-insurance', name: 'add_insurance_to_order', methods: ['POST'])]
    public function addInsuranceToOrder(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof Customer) {
            throw new AccessDeniedException('Only customer can update orders.');
        }

        try {
            $order = $this->addInsuranceToOrderUseCase->execute($id, $user->getId());

            return $this->json([
                'id' => $order->getId(),
                'status' => $order->getStatus(),
                'totalAmount' => $order->getTotalAmount(),
                'creationDate' => $order->getCreationDate()->format('Y-m-d H:i:s'),
                'customer' => [
                    'id' => $order->getCustomer()->getId(),
                    'firstName' => $order->getCustomer()->getFirstName(),
                    'lastName' => $order->getCustomer()->getLastName()
                ],
                'insurance' => $order->getInsurance() ? [
                    'id' => $order->getInsurance()->getId(),
                    'price' => $order->getInsurance()->getPrice(),
                    'description' => $order->getInsurance()->getDescription()
                ] : null,
                'message' => 'Insurance added to the order.'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], $e instanceof AccessDeniedException ? Response::HTTP_FORBIDDEN : Response::HTTP_BAD_REQUEST);
        }
    }
}