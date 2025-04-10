<?php

namespace App\Application\Auth\Register;

/**
 * Value object représentant une demande d'enregistrement
 */
class RegisterRequest
{
    private string $email;
    private string $password;
    private string $firstName;
    private string $lastName;
    private ?\DateTimeInterface $licenseObtainmentDate;

    public function __construct(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        ?\DateTimeInterface $licenseObtainmentDate = null
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->licenseObtainmentDate = $licenseObtainmentDate;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getLicenseObtainmentDate(): ?\DateTimeInterface
    {
        return $this->licenseObtainmentDate;
    }
}