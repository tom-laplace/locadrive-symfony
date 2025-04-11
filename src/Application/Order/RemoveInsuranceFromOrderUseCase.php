<?php

namespace App\Application\Order;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class RemoveInsuranceFromOrderUseCase
{
    private $entityManager;
    private $orderRepository;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
    }

    public function execute($orderId, $customerId): Order
    {
        $order = $this->orderRepository->findOneById($orderId);

        if (!$order) {
            throw new Exception("Can not find order.");
        }

        if ($order->getCustomer()->getId() != $customerId) {
            throw new Exception("Can not remove insurance from the order.");
        }

        try {
            $order->removeInsurance();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        try {
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception("Error while trying to remove insurance from the order.");
        }

        return $order;
    }

}