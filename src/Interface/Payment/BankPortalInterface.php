<?php

namespace App\Interface\Payment;

use App\DTO\Payment\PaymentDTO;
use App\Repository\Payment\PaymentRepository;

interface BankPortalInterface
{
    public function call(PaymentDTO $requestDto,PaymentRepository $repository);

    public function DtoToEntity(PaymentDTO $requestDto);
}