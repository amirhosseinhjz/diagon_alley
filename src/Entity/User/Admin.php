<?php

namespace App\Entity\User;

use App\Repository\UserRepository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{
    const ADMIN = 'ROLE_ADMIN';

    public function getRoles(): array
    {
        return [self::ADMIN];
    }

    public function eraseCredentials()
    {

    }

    public function getUserIdentifier() : string
    {
        return $this->getPhoneNumber();
    }

}
