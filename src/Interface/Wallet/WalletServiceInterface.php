<?php

namespace App\Interface\Wallet;

use App\Entity\User\User;

interface WalletServiceInterface
{
    public function create(User $user);

    public function withdraw(int $walletId, int $amount);

    public function deposit(int $walletId, int $amount);

    public function transaction(int $payerId, int $beneficiaryId, int $amount);
}