<?php

namespace App\Service\Payment;

use App\DTO\Payment\PaymentDTO;
use App\Entity\Order\Purchase;
use App\Entity\Payment\Payment;
use App\Entity\User\Customer;
use App\Entity\Wallet\Wallet;
use App\Interface\Payment\paymentInterface;
use App\Service\OrderService\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

abstract class PaymentService implements paymentInterface
{
    protected Serializer $serializer;
    public function __construct(
        protected EntityManagerInterface $em,
        protected ValidatorInterface $validator,
        protected OrderService $orderService,
    ) {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    public function dtoFromOrderArray($array)
    {
        if(array_key_exists("purchase",$array))
        {
            $purchase = $this->em->getRepository(Purchase::class)->find($array["purchase"]);
            if (is_null($purchase))
                throw (new \Exception("This order is not exist."));
            if ($purchase->getStatus() != Purchase::STATUS_PENDING)
                throw (new \Exception("This order is not suitable for payment."));
            $array["purchase"] = $purchase;
            if($array["method"]=="wallet")
            {
                $wallet = $purchase->getCustomer()->getWallet();
                $array["wallet"] = $wallet;
            }
            $array["paidAmount"] = $purchase->getTotalPrice();
        }
        else if(array_key_exists("wallet",$array))
        {
            $wallet = $this->em->getRepository(Wallet::class)->find($array["wallet"]);
            if (is_null($wallet))
                throw (new \Exception("This wallet is not exist."));
            $array["wallet"] = $wallet;
        }
        
        $paymentDTO = new PaymentDTO();
        foreach ($array as $key => $value) {
            $paymentDTO->$key = $value;
        }
        
        $DTOErrors = $this->validate($paymentDTO);
        if (count($DTOErrors) > 0) {
            throw (new \Exception(json_encode($DTOErrors)));
        }

        return $paymentDTO;
    }

    public function entityFromDto(PaymentDTO $paymentDto, $flash = true)
    {
        $payment = new Payment();

        foreach ($paymentDto as $key => $value) {
            $setterName = 'set' . ucfirst($key);
            $payment->$setterName($value);
        }
        $this->em->persist($payment);

        if ($flash)
            $this->em->flush();

        return $payment->getId();
    }

    protected function validate($object): array
    {
        $errors = $this->validator->validate($object);
        $errorsArray = [];
        foreach ($errors as $error) {
            $errorsArray[] = $error->getMessage();
        }
        return $errorsArray;
    }
}
