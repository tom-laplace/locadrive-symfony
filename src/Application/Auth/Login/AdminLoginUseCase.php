<?php

namespace App\Application\Auth\Login;

use App\Application\Service\PasswordManager;
use App\Entity\Administrator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AdminLoginUseCase
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

    public function execute(LoginRequest $loginRequest): Administrator
    {
        $email = $loginRequest->getEmail();
        $password = $loginRequest->getPassword();

        $adminRepository = $this->entityManager->getRepository(Administrator::class);
        $admin = $adminRepository->findOneBy(["email" => $email]);

        if (!$admin || !$this->passwordManager->verify($password, $admin->getPassword())) {
            throw new AuthenticationException("Email or password invalid.");
        }

        $token = new UsernamePasswordToken(
            $admin,
            'main',
            $admin->getRoles()
        );

        $this->tokenStorage->setToken($token);

        return $admin;
    }
}