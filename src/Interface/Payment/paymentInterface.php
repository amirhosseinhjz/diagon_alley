<?php

namespace App\Interface\Payment;

use App\DTO\Payment\PaymentDTO;

interface paymentInterface
{
    public function dtoFromOrderArray($array);

    public function entityFromDto(PaymentDTO $paymentDto);

    public function pay(PaymentDTO $paymentDto, $array);
}
