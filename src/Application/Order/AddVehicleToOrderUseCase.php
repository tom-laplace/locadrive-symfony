<?php

namespace App\Application\Order;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Vehicle;
use App\Repository\OrderRepository;
use App\Repository\VehicleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AddVehicleToOrderUseCase
{
    private $entityManager;
    private $orderRepository;
    private $vehicleRepository;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository, VehicleRepository $vehicleRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function execute(
        Customer $customer,
        int $vehicleId,
        DateTime $startDate,
        DateTime $endDate,
        ?int $orderId = null
    ): Order {

        $order = null;
        if ($orderId) {
            $order = $this->orderRepository->findOneById($orderId);

            if ($order->getStatus() !== "CART") {
                throw new Exception("Impossible to update this order : not in cart");
            }

            if ($order->getCustomer()->getId() !== $customer->getId()) {
                throw new Exception("Can't update order.");
            }
        } else {
            try {
                $order = new Order($customer);
                $this->entityManager->persist($order);
                $this->entityManager->flush();
            } catch (Exception $e) {
                throw new Exception("Error while creating the order.");
            }
        }

        $vehicle = $this->vehicleRepository->findOneById($vehicleId);
        if (!$vehicle) {
            throw new BadRequestException("Can't find vehicle.");
        }

        // $this->checkVehicleAvailability($vehicle, $startDate, $endDate);

        try {
            $orderItem = new OrderItem($order, $vehicle, $startDate, $endDate);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        
        $order->addOrderItem($orderItem);

        try {
            $this->entityManager->persist($orderItem);
            $this->entityManager->flush();

            $this->entityManager->persist($order);
            $this->entityManager->flush();

        } catch (Exception $e) {
            throw new Exception("Error while adding vehicle to order");
        }

        return $order;
    }
}