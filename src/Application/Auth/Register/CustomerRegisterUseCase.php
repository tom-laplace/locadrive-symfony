<?php

namespace App\Application\Auth\Register;

use App\Entity\Customer;
use App\Application\Service\PasswordManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CustomerRegisterUseCase
{
    private EntityManagerInterface $entityManager;
    private PasswordManager $passwordManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        PasswordManager $passwordManager
    ) {
        $this->entityManager = $entityManager;
        $this->passwordManager = $passwordManager;
    }

    public function execute(RegisterRequest $registerRequest): Customer
    {
        $userRepository = $this->entityManager->getRepository(Customer::class);
        $existingUser = $userRepository->findOneBy(['email' => $registerRequest->getEmail()]);

        if ($existingUser) {
            throw new Exception('Invalid email.');
        }

        $customer = new Customer(
            $registerRequest->getEmail(),
            $this->passwordManager->hash($registerRequest->getPassword()),
            $registerRequest->getFirstName(),
            $registerRequest->getLastName(),
            $registerRequest->getLicenseObtainmentDate()
        );

        try {
            $this->entityManager->persist($customer);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception('Error while creating user. Please try again.');
        }

        return $customer;
    }


}