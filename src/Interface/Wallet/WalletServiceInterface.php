<?php

namespace App\Interface\Wallet;

use App\Entity\User\User;

interface WalletServiceInterface
{
    public function create(User $user);
}