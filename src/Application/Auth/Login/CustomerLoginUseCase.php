<?php

namespace App\Application\Auth\Login;

use App\Application\Service\PasswordManager;
use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class CustomerLoginUseCase
{
    private EntityManagerInterface $entityManager;
    private PasswordManager $passwordManager;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        PasswordManager $passwordManager,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->entityManager = $entityManager;
        $this->passwordManager = $passwordManager;
        $this->jwtManager = $jwtManager;
    }

    public function execute(LoginRequest $loginRequest): array
    {
        $email = $loginRequest->getEmail();
        $password = $loginRequest->getPassword();

        $customerRepository = $this->entityManager->getRepository(Customer::class);
        $customer = $customerRepository->findOneBy(["email" => $email]);

        if (!$customer || !$this->passwordManager->verify($password, $customer->getPassword())) {
            throw new AuthenticationException("Email or password invalid.");
        }

        $token = $this->jwtManager->create($customer);

        return [
            'customer' => $customer,
            'token' => $token
        ];
    }
}