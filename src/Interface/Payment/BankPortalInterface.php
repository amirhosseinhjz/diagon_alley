<?php

namespace App\Interface\Payment;

use App\DTO\Payment\PaymentDTO;

interface BankPortalInterface
{
    public function call(PaymentDTO $requestDto);

    public function DtoToEntity(PaymentDTO $requestDto);
}