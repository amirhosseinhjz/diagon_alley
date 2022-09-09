<?php

namespace App\Interface\Portal;

use App\DTO\Payment\PaymentDTO;

interface portalInterface
{
    public function payOrder(PaymentDTO $paymentDTO);
}
