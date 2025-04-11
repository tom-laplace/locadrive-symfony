<?php

namespace App\Application\Order;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class RemoveInsuranceFromOrderUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute($orderId, $customerId): Order
    {
        $order = $this->entityManager->getRepository(Order::class)->find($orderId);

        if (!$order) {
            throw new Exception("Can not find order.");
        }

        if ($order->getCustomer()->getId() != $customerId) {
            throw new Exception("Can not remove insurance from the order.");
        }

        try {
            $order->removeInsurance();

            $this->entityManager->persist($order);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception("Error while trying to remove insurance from the order.");
        }

        return $order;
    }

}