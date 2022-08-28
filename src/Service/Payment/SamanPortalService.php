<?php

namespace App\Service\Payment;

use App\Interface\Payment\BankPortalInterface;
use App\Repository\Payment\PaymentRepository;
use App\DTO\Payment\PaymentDTO;
use App\Entity\Payment\Payment;


class SamanPortalService implements BankPortalInterface
{
    public function call(PaymentDTO $requestDto, PaymentRepository $repository)
    {
        //TODO -> http request to test bank


        //TODO: set new status and code

        $this->DtoToEntity($requestDto, $repository);

        //TODO: return payment
    }

    public function DtoToEntity(PaymentDTO $requestDto)
    {
        $payment = new Payment();
        $payment->setType($requestDto->type);
        $payment->setPaidAmount($requestDto->paidAmount);
        $payment->setStatus($requestDto->status);
        $payment->setCode($requestDto->code);

        $this->repository->add($payment, true);

    }
}