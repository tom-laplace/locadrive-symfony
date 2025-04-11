<?php

namespace App\Application\Order;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Vehicle;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AddVehicleToOrderUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
            $order = $this->entityManager->getRepository(Order::class)->find($orderId);

            if ($order->getStatus() !== "CART") {
                throw new Exception("Impossible to update this order : not in cart");
            }

            if ($order->getCustomer()->getId() !== $customer->getId()) {
                throw new Exception("Can't update order.");
            }
        } else {
            $order = new Order();
            $order->setCustomer($customer);
            $order->setStatus('CART');
            $order->setTotalAmount(0);
            $order->setCreationDate(new DateTime());

            $this->entityManager->persist($order);
            $this->entityManager->flush();
        }

        $vehicle = $this->entityManager->getRepository(Vehicle::class)->find($vehicleId);
        if (!$vehicle) {
            throw new BadRequestException("Can't find vehicle.");
        }

        $this->checkVehicleAvailability($vehicle, $startDate, $endDate);

        $interval = $startDate->diff($endDate);
        $days = $interval->days + 1;
        $price = $vehicle->getDailyRate() * $days;

        $orderItem = new OrderItem($order, $vehicle, $startDate, $endDate, $price);


        $order->setTotalAmount($order->getTotalAmount() + $price);

        try {
            $this->entityManager->persist($orderItem);
            $this->entityManager->flush();

        } catch (Exception $e) {
            throw new Exception("Error while adding vehicle to order");
        }

        return $order;
    }

    private function checkVehicleAvailability(Vehicle $vehicle, DateTime $startDate, DateTime $endDate, ?int $excludeOrderId = null): void
    {
        $orderItemRepository = $this->entityManager->getRepository(OrderItem::class);

        $qb = $orderItemRepository->createQueryBuilder('oi')
            ->join('oi.orderRef', 'o')
            ->join('oi.vehicle', 'v')
            ->where('v.id = :vehicleId')
            ->andWhere('o.status != :statusCart OR (o.status = :statusCart AND o.id != :excludeOrderId)')
            ->andWhere(
                '(oi.startDate <= :endDate AND oi.endDate >= :startDate)'
            )
            ->setParameter('vehicleId', $vehicle->getId())
            ->setParameter('statusCart', 'CART')
            ->setParameter('excludeOrderId', $excludeOrderId)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        $conflictingItems = $qb->getQuery()->getResult();

        if (count($conflictingItems) > 0) {
            throw new BadRequestException('This vehicule is not available for this duration.');
        }
    }
}