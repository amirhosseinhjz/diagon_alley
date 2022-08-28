<?php

namespace App\Entity\User;

use App\Repository\UserRepository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer extends User
{
    const CUSTOMER = 'ROLE_CUSTOMER';

    public function getRoles(): array
    {
        return [self::CUSTOMER];
    }


    public function eraseCredentials()
    {

    }

    public function getUserIdentifier() : string
    {
        return $this->getPhoneNumber();
    }
}
