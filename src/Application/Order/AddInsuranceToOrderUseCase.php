<?php

namespace App\Application\Order;

use App\Entity\Insurance;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddInsuranceToOrderUseCase
{
    private $entityManager;
    private $orderRepository;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
    }

    public function execute($orderId, $customerId)
    {
        $order = $this->orderRepository->findOneById($orderId);

        if (!$order) {
            throw new \Exception("Could not find order.");
        }

        if ($order->getCustomer()->getId() !== $customerId) {
            throw new \Exception("Can't add insurance to the order.");
        }

        try {
            $insurance = new Insurance();
            $this->entityManager->persist($insurance);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception("Error while creating the insurance.");
        }

        try {
            $order->addInsurance($insurance);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        try {
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception("Error while adding insurance to the order");
        }

        return $order;
    }
}