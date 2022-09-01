<?php

namespace App\Service\Payment;

use App\Interface\Payment\BankPortalInterface;
use App\Repository\Payment\PaymentRepository;
use App\DTO\Payment\PaymentDTO;
use App\Entity\Payment\Payment;
use App\Service\CartService\CartServiceInterface;

abstract class BankPortalService implements BankPortalInterface
{
    protected CartServiceInterface $cartManager;
    private $terminalId;
    private $userName;
    private $password;

    public function __construct(CartServiceInterface $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    public function makePaymentDTO($cartId, $type, $validator, $repository)
    {

        $cart =  $this->cartManager->getCartById($cartId);

        if (is_null($cart))
            throw (new \Exception("This cart is not exist."));

        $price = $this->cartManager->getTotalPrice($cartId);
        $paymentDTO = new PaymentDTO($cart, $price, $type, $validator);
        $this->DtoToEntity($paymentDTO, $repository);

        return $paymentDTO;
    }

    public function payCart(PaymentDTO $paymentDTO)
    {
        if ($paymentDTO->cart->getStatus() == "INIT") {
            $this->cartManager->updateStatus($paymentDTO->cart->getId(), "PENDING");

            $this->setInitial();

            $token = $this->getToken($paymentDTO);

            return $this->directToPayment($token);
        } else {
            throw (new \Exception("Payment is not in the correct stage"));
        }
    }

    public function DtoToEntity(PaymentDTO $requestDto, PaymentRepository $repository)
    {
        $payment = new Payment();

        foreach ($requestDto as $key => $value) {
            $setterName = 'set' . ucfirst($key);
            $payment->$setterName($value);
        }

        $repository->add($payment, true);
    }
}
