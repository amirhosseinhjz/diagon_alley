<?php

namespace App\Interface\Payment;

use App\DTO\Payment\PaymentDTO;
use App\Repository\Payment\PaymentRepository;
use App\Service\CartManager;

interface BankPortalInterface
{
    // public function __construct();

    public function makePaymentDTO($cartId,$type,$validator);
    
    public function payCart(PaymentDTO $paymentDTO);

    public function setInitial();

    public function getToken(PaymentDTO $paymentDTO);

    public function directToPayment($token);

    public function checkStatus($result);

    public function DtoToEntity(PaymentDTO $requestDto);
}