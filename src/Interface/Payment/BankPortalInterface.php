<?php

namespace App\Interface\Payment;

use App\DTO\Payment\PaymentDTO;
use App\Repository\Payment\PaymentRepository;

interface BankPortalInterface
{
    public function makePaymentDTO($cartId, $type, $validator, PaymentRepository $repository);

    public function payCart(PaymentDTO $paymentDTO);

    public function setInitial();

    public function getToken(PaymentDTO $paymentDTO);

    public function directToPayment($token);

    public function changeStatus($result, $repository);

    public function DtoToEntity(PaymentDTO $requestDto, PaymentRepository $repository);
}
