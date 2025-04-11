<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer extends User
{

    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $licenseObtainmentDate = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'customer')]
    private Collection $orders;

    public function __construct($email, $password, $firstName, $lastName, $licenseObtainmentDate)
    {
        $this->validatePassword($password);
        $this->validateNewCustomerArguments($firstName, $lastName, $licenseObtainmentDate);

        $this->$email = $email;
        $this->$password = $password;
        $this->$firstName = $firstName;
        $this->$lastName = $lastName;
        $this->$licenseObtainmentDate = $licenseObtainmentDate;
        $this->$roles = ['ROLE_CUSTOMER'];

        $this->orders = new ArrayCollection();
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getLicenseObtainmentDate(): ?\DateTimeInterface
    {
        return $this->licenseObtainmentDate;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setCustomer($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getCustomer() === $this) {
                $order->setCustomer(null);
            }
        }

        return $this;
    }

    private function validatePassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new Exception('Password should be at least composed of 8 characters.');
        }

        if (strlen(preg_replace('/[^0-9]/', '', $password)) < 4) {
            throw new Exception('Password should have at least 4 numbers');
        }

        if (strlen(preg_replace('/[^a-zA-Z]/', '', $password)) < 4) {
            throw new Exception('Password should have at least 4 letters');
        }
    }

    private function validateNewCustomerArguments($firstName, $lastName, $licenseObtainmentDate)
    {
        if (!$firstName || !$lastName || !$licenseObtainmentDate) {
            throw new InvalidArgumentException('Mising an argument to create new customer.');
        }
    }
}
