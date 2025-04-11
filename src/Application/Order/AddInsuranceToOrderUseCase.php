<?php

namespace App\Application\Order;

use App\Entity\Insurance;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class AddInsuranceToOrderUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute($orderId, $customerId)
    {
        $order = $this->entityManager->getRepository(Order::class)->find($orderId);

        if (!$order) {
            throw new \Exception("Could not find order.");
        }

        if ($order->getCustomer()->getId() !== $customerId) {
            throw new \Exception("Can't add insurance to the order.");
        }

        if (!empty($order->getInsurance())) {
            throw new \Exception("This order is already assured.");
        }

        if ($order->getStatus() !== "CART") {
            throw new \Exception("Impossible to update this order : not in cart");
        }

        try {
            $insurance = new Insurance();
            $this->entityManager->persist($insurance);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception("Error while creating the insurance for the order");
        }

        $order->setInsurance($insurance);

        try {
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception("Error while adding insurance to the order");
        }

        return $order;
    }
}