<?php

namespace App\Service\Payment;

use App\Interface\Payment\BankPortalInterface;
use App\Repository\Payment\PaymentRepository;
use App\DTO\Payment\PaymentDTO;
use App\Entity\Payment\Payment;
use App\Service\CartManager;
use Doctrine\ORM\EntityManagerInterface;

abstract class BankPortalService implements BankPortalInterface
{

    public function __construct(
        protected CartManager $cartManager,
        private EntityManagerInterface $em){}
    
    public function makePaymentDTO($cartId,$type,$validator,$repository){

        $cart =  $this->cartManager->getCart($cartId,false);

        $paymentDTO = new PaymentDTO($cart, $type, $validator);

        $this->DtoToEntity($paymentDTO,$repository);

        return $paymentDTO;
    }

    public function payCart(PaymentDTO $paymentDTO){
        if($paymentDTO->cart->getStatus()=="INIT")
        {
            $this->cartManager->updateStatus($paymentDTO->cart->getId(), "PENDING");

            $this->setInitial();
    
            $token = $this->getToken($paymentDTO);
            
            return $this->directToPayment($token);
        }
        else
        {
            throw (new \Exception("Payment is not in the correct stage"));
        }
    }

    public function changeStatus($result,$repository)
    {
        $payment = $repository->findOneById($result['ResNum']);
        
        if($result["State"] == "OK")
        {
            $payment->setStatus("SUCCESS");
            $payment->getCart()->setStatus("SUCCESS");
        }
        else
        {
            $payment->setStatus("FAILED");
            $payment->getCart()->setStatus("INIT");
        }
        
        $payment->setCode($result['TraceNo']);
        $repository->getEntityManager()->flush();
        
        return [$payment->getCart()->getId(),$result["State"]];
    }


    public function DtoToEntity(PaymentDTO $requestDto,PaymentRepository $repository)
    {
        $payment = new Payment();
        $payment->setType($requestDto->type);
        $payment->setPaidAmount($requestDto->paidAmount);
        $payment->setStatus($requestDto->status);
        $payment->setCode($requestDto->code);

        $repository->add($payment, true);
    }
}