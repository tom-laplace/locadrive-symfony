<?php

namespace App\Application\Order;

use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class RemoveVehicleFromOrderUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute($orderId, $orderItemId, $customerId): Order
    {
        $order = $this->entityManager->getRepository(Order::class)->find($orderId);
        $orderItem = $this->entityManager->getRepository(OrderItem::class)->find($orderItemId);

        if (!$order) {
            throw new Exception("Can not find order.");
        }

        if (!$orderItem) {
            throw new Exception("Can not find order item.");
        }

        if ($order->getCustomer()->getId() != $customerId) {
            throw new Exception("Can not remove insurance from the order.");
        }

        if (!$order->getOrderItems()->contains($orderItem)) {
            throw new Exception("This item is not part of your order.");
        }

        try {
            $order->removeOrderItem($orderItem);

            $this->entityManager->persist($order);
            $this->entityManager->flush();

            $this->entityManager->remove($orderItem);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $order;
    }
}