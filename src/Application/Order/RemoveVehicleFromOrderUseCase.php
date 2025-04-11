<?php

namespace App\Application\Order;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class RemoveVehicleFromOrderUseCase
{
    private $entityManager;
    private $orderRepository;
    private $orderItemRepository;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository, OrderItemRepository $orderItemRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    public function execute($orderId, $orderItemId, $customerId): Order
    {
        $order = $this->orderRepository->findOneById($orderId);
        $orderItem = $this->orderItemRepository->findOneById($orderItemId);

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
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        try {
            $this->entityManager->persist($order);
            $this->entityManager->flush();

            $this->entityManager->remove($orderItem);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception("Error while trying to remove the order item from your order");
        }

        return $order;
    }
}