<?php

namespace App\Application\Order;

use App\Entity\Order;
use App\Entity\PaymentMethod;
use App\Repository\OrderRepository;
use App\Repository\PaymentMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class AddPaymentMethodToOrderUseCase
{
    private $entityManager;
    private $orderRepository;
    private $paymentMethodRepository;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository, PaymentMethodRepository $paymentMethodRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function execute($paymentMethodId, $orderId, $customerId): Order
    {
        $order = $this->orderRepository->findOneById($orderId);

        if (!$order) {
            throw new Exception("Could not find order.");
        }

        if ($order->getCustomer()->getId() !== $customerId) {
            throw new Exception("Can't add insurance to the order.");
        }

        $paymentMethod = $this->paymentMethodRepository->findOneById($paymentMethodId);

        if (!$paymentMethod) {
            throw new Exception("Payment method not found.");
        }

        try {
            $order->addPaymentMethod($paymentMethod);
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }

        try {
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception("Error while adding payment method.");
        }

        return $order;
    }
}