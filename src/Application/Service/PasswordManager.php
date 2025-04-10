<?php

namespace App\Application\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class PasswordManager
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function hash(string $plainPassword, ?PasswordAuthenticatedUserInterface $user = null)
    {
        if ($user) {
            return $this->passwordHasher->hashPassword($user, $plainPassword);
        }

        $anonymousUser = new class implements PasswordAuthenticatedUserInterface {
            public function getPassword(): ?string
            {
                return null;
            }
        };

        return $this->passwordHasher->hashPassword($anonymousUser, $plainPassword);
    }

    public function verify(string $plainPassword, string $hashedPassword): bool
    {
        $anonymousUser = new class ($hashedPassword) implements PasswordAuthenticatedUserInterface {
            private string $password;

            public function __construct(string $password)
            {
                $this->password = $password;
            }

            public function getPassword(): ?string
            {
                return $this->password;
            }
        };

        return $this->passwordHasher->isPasswordValid($anonymousUser, $plainPassword);
    }
}