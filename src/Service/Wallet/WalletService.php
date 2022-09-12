<?php

namespace App\Service\Wallet;

use App\Entity\User\Customer;
use App\Entity\User\Seller;
use App\Entity\Wallet\Wallet;
use App\Interface\Wallet\WalletServiceInterface;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class WalletService implements WalletServiceInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(User $user)
    {
        $wallet = new Wallet();
        $wallet->setBalance(0);
        if ($user instanceof Customer) {
            $wallet->setCustomer($user);
            $this->em->getRepository(Wallet::class)->add($wallet, true);
        }
        if ($user instanceof Seller) {
            $wallet->setSeller($user);
            $this->em->getRepository(Wallet::class)->add($wallet, true);
        }
        throw new Exception('user type is not valid');
    }
}