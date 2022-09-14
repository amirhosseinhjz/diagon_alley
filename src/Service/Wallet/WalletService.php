<?php

namespace App\Service\Wallet;

use App\DTO\Payment\PaymentDTO;
use App\Entity\Payment\Payment;
use App\Entity\User\Customer;
use App\Entity\User\Seller;
use App\Entity\Wallet\Wallet;
use App\Interface\Wallet\WalletServiceInterface;
use App\Entity\User\User;
use App\Service\Payment\PaymentService;
use Exception;

class WalletService extends PaymentService implements WalletServiceInterface
{

    public function create(User $user)
    {
        $wallet = new Wallet();
        $wallet->setBalance(0);
        if ($user instanceof Customer) {
            $wallet->setCustomer($user);
            $this->em->getRepository(Wallet::class)->add($wallet, true);
        }
        else if ($user instanceof Seller) {
            $wallet->setSeller($user);
            $this->em->getRepository(Wallet::class)->add($wallet, true);
        }
        else
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
        $payment = $this->em->getRepository(Payment::class)->find($array["payment"]);

        $wallet = $paymentDto->purchase->getCustomer()->getWallet();
        $amount = $paymentDto->paidAmount;
        $purchase = $paymentDto->purchase;

        if ($wallet->getBalance() < $amount) {
            $payment->setStatus("FAILED");
            return ["type" => "payOrder","Id" => $purchase->getId(), "Status" => 'not enough balance'];
        }

        $wallet->deposit($amount);
        $this->em->getRepository(Wallet::class)->add($wallet, false);
        $payment->setStatus("SUCCESS");

        $this->orderService->finalizeOrder($purchase);
        $this->em->flush();
        return ["type" => "payOrder", "Id" => $purchase->getId(), "Status" => 'OK'];
    }

    public function getUserId(int $walletId)
    {
        $wallet = $this->em->getRepository(Wallet::class)->find($walletId);

        if(is_null($wallet->getCustomer()))
            return $wallet->getSeller()->getId();
        return $wallet->getCustomer()->getId();
    }

    public function getByUser(User $user): Wallet
    {
        if ($user instanceof Customer) {
            return $this->em->getRepository(Wallet::class)->findOneBy(['customer' => $user]);
        }
        else if ($user instanceof Seller) {
            return $this->em->getRepository(Wallet::class)->findOneBy(['seller' => $user]);
        }
        else
            throw new Exception('user type is not valid');
    }

    public function addMoney(Wallet $wallet, int $amount)
    {
        $wallet->deposit($amount);
        $this->em->persist($wallet);
        $this->em->flush();
    }
}