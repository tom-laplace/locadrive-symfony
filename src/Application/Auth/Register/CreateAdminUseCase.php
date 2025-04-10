<?php

namespace App\Application\Auth\Register;

use App\Entity\Administrator;
use App\Application\Service\PasswordManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CreateAdminUseCase
{
    private EntityManagerInterface $entityManager;
    private PasswordManager $passwordManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        PasswordManager $passwordManager,
    ) {
        $this->entityManager = $entityManager;
        $this->passwordManager = $passwordManager;
    }
    public function execute(RegisterRequest $registerRequest): Administrator
    {
        $userRepository = $this->entityManager->getRepository(Administrator::class);
        $existingUser = $userRepository->findOneBy(['email' => $registerRequest->getEmail()]);

        if ($existingUser) {
            throw new Exception('Invalid email.');
        }

        $admin = new Administrator();
        $admin->setEmail($registerRequest->getEmail());
        $admin->setPassword($this->passwordManager->hash($registerRequest->getPassword()));
        $admin->setRoles(['ROLE_ADMIN']);

        try {
            $this->entityManager->persist($admin);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception('Error while creating the administrator');
        }

        return $admin;
    }
}