<?php

namespace App\Application\Auth\Login;

use App\Application\Service\PasswordManager;
use App\Entity\Administrator;
use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class CustomerLoginUseCase
{
    private EntityManagerInterface $entityManager;
    private PasswordManager $passwordManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        EntityManagerInterface $entityManager,
        PasswordManager $passwordManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->entityManager = $entityManager;
        $this->passwordManager = $passwordManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function execute(LoginRequest $loginRequest): Customer
    {
        $email = $loginRequest->getEmail();
        $password = $loginRequest->getPassword();

        $customerRepository = $this->entityManager->getRepository(Customer::class);
        $customer = $customerRepository->findOneBy(["email" => $email]);

        if (!$customer || !$this->passwordManager->verify($password, $customer->getPassword())) {
            throw new AuthenticationException("Email or password invalid.");
        }

        $token = new UsernamePasswordToken(
            $customer,
            'main',
            $customer->getRoles()
        );

        $this->tokenStorage->setToken($token);

        return $customer;
    }
}