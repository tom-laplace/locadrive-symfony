<?php

namespace App\Application\Order;

use App\Entity\Order;
use App\Entity\PaymentMethod;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class AddPaymentMethodToOrderUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute($paymentMethodId, $orderId, $customerId): Order
    {
        $order = $this->entityManager->getRepository(Order::class)->find($orderId);

        if (!$order) {
            throw new Exception("Could not find order.");
        }

        if ($order->getStatus() !== "CART") {
            throw new Exception("Impossible to update this order : not in cart");
        }

        if ($order->getCustomer()->getId() !== $customerId) {
            throw new Exception("Can't add insurance to the order.");
        }

        $paymentMethod = $this->entityManager->getRepository(PaymentMethod::class)->find($paymentMethodId);

        $order->setPaymentMethod($paymentMethod);

        try {
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception("Error while adding payment method.");
        }

        return $order;
    }
}