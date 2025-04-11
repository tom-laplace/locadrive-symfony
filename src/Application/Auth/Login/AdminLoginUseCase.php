<?php

namespace App\Application\Auth\Login;

use App\Application\Service\PasswordManager;
use App\Entity\Administrator;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AdminLoginUseCase
{
    private EntityManagerInterface $entityManager;
    private PasswordManager $passwordManager;
    private JWTTokenManagerInterface $jwtManager;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        PasswordManager $passwordManager,
        JWTTokenManagerInterface $jwtManager,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->passwordManager = $passwordManager;
        $this->jwtManager = $jwtManager;
        $this->userRepository = $userRepository;
    }

    public function execute(LoginRequest $loginRequest): array
    {
        $email = $loginRequest->getEmail();
        $password = $loginRequest->getPassword();

        $admin = $this->userRepository->findOneByEmail($email);

        if (!$admin || !$this->passwordManager->verify($password, $admin->getPassword())) {
            throw new AuthenticationException("Email or password invalid.");
        }

        $token = $this->jwtManager->create($admin);

        return [
            'admin' => $admin,
            'token' => $token
        ];
    }
}