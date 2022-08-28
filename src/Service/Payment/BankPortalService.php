<?php

namespace App\Service\Payment;

use App\Interface\Payment\BankPortalInterface;
use App\Repository\Payment\PaymentRepository;
use App\DTO\Payment\PaymentDTO;
use App\Entity\Payment\Payment;
use App\Service\CartManager;

abstract class BankPortalService implements BankPortalInterface
{
    protected $cartManager;
    
    private $terminalId;
    private $token;
    
    public function makePaymentDTO($cartId,$type,$validator){
        // $this->cartManager = new CartManager();
        // $cart =  $this->cartManager->getCart($cartId,false);
        // dd($cart);
        $price = 2000;

        $paymentDTO = new PaymentDTO($cartId,$price, $type, $validator);

        return $paymentDTO;
    }

    public function payCart(PaymentDTO $paymentDTO){
        // if($paymentDTO->cart->getStatus()=="INIT")
        // {
            // $cartManager->updateStatus($paymentDTO->cart->getId(), "PENDING");

            $this->setInitial();
    
            $token = $this->getToken($paymentDTO);
            
            return $this->directToPayment($token);
        // }
        // else
        // {
            // throw (new \Exception("Payment is not in the correct stage"));
        // }
    }

    public function checkStatus($result)
    {
        // if($result["State"] == "OK")
        // {
            
        //     $this->PaymentDTO->cart->updateStatus("SUCCESS");
        //     $this->PaymentDTO->status="SUCCESS";
        // }
        return $result["State"];
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