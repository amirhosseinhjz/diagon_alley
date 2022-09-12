<?php

namespace App\Service\Wallet;

use App\DTO\Payment\PaymentDTO;
use App\Entity\User\Customer;
use App\Entity\User\Seller;
use App\Entity\Wallet\Wallet;
use App\Interface\Wallet\WalletServiceInterface;
use App\Entity\User\User;
use App\Service\Payment\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class WalletService extends PaymentService implements WalletServiceInterface
{
    protected EntityManagerInterface $em;

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

    public function withdraw(int $walletId, int $amount)
    {
        $wallet = $this->em->getRepository(Wallet::class)->findOneById($walletId);
        $wallet->withdraw($amount);
        $this->em->getRepository(Wallet::class)->add($wallet, false);
    }

    public function deposit(int $walletId, int $amount)
    {
        $wallet = $this->em->getRepository(Wallet::class)->findOneById($walletId);
        $wallet->deposit($amount);
        $this->em->getRepository(Wallet::class)->add($wallet, false);
    }

    public function transaction(int $payerId, int $beneficiaryId, int $amount)
    {
        $payer = $this->em->getRepository(Wallet::class)->findOneById($payerId);
        $beneficiary = $this->em->getRepository(Wallet::class)->findOneById($beneficiaryId);
        $payer->deposit($amount);
        $beneficiary->withdraw($amount);
        $this->em->flush();
    }

    public function pay(PaymentDTO $paymentDto, $array)
    {

    }
}