<?php

namespace App\Service\Payment;

use App\Entity\Payment\Payment;
use App\Repository\Payment\PaymentRepository;
use App\DTO\Payment\PaymentDTO;

class PaymentService
{
    public function __construct(
        private readonly PaymentRepository $repository)
    {
    }

    public function new(PaymentDTO $requestDto): Payment
    {
        //TODO -> logic of payment
        
        $payment = new Payment();
        $payment->setType($requestDto->type);
        $payment->setPaidAmount($requestDto->paidAmount);
        // $payment->setCreatedAt($requestDto->createdAt);
        $payment->setStatus($requestDto->status);
        $payment->setCode($requestDto->code);

        $this->repository->add($payment, true);
        // $client = new \nusoap_client();
        // $client = new \nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');

        return $payment;
    }
}