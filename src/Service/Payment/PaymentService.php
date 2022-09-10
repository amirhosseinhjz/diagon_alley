<?php

namespace App\Service\Payment;

use App\Entity\User\User;
use App\DTO\Payment\PaymentDTO;
use App\Entity\Order\Purchase;
use App\Entity\Payment\Payment;
use App\Interface\Payment\paymentInterface;
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
    ) {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    public function dtoFromOrderArray($array)
    {
        $purchase = $this->em->getRepository(Purchase::class)->find($array["purchase"]);
        if (is_null($purchase))
            throw (new \Exception("This order is not exist."));
        if ($purchase->getStatus() != Purchase::STATUS_PENDING)
            throw (new \Exception("This order is not suitable for payment."));
        unset($array["purchase"]); 

        $array["paidAmount"] = $purchase->getTotalPrice();
        $paymentDTO = $this->serializer->deserialize(json_encode($array), PaymentDTO::class, 'json');
        $paymentDTO->purchase = $purchase;
        
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
