<?php

namespace App\Application\Order;

use App\Entity\Order;
use App\Entity\Payment;
use App\Repository\OrderRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PayOrderUseCase
{
    private $entityManager;
    private $orderRepository;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
    }

    public function execute(int $orderId, int $customerId): Order
    {
        $order = $this->orderRepository->findOneById($orderId);

        if (!$order) {
            throw new Exception("Can not find order.");
        }

        if ($order->getCustomer()->getId() !== $customerId) {
            throw new Exception("Error while paying for the order.");
        }

        try {
            $payment = new Payment($order->getTotalAmount());
            $this->entityManager->persist($payment);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception("Error while trying to initiate the payment.");
        }

        try {
            $order->pay($payment);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        try {
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception("Error while trying to pay the order;");
        }

        return $order;
    }
}