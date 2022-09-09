<?php

namespace App\Service\Payment;

use App\DTO\Payment\PaymentDTO;
use App\Entity\Payment\Payment;
use App\Interface\Payment\paymentInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

abstract class PaymentService implements paymentInterface
{
    private Serializer $serializer;
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
    ) {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    public function dtoFromOrderArray($array)
    {
        $order = $this->em->getRepository(Order::class)->find($array["order"]);
        if (is_null($order))
            throw (new \Exception("This order is not exist."));
        if ($order->getStatus() != "PENDING")
            throw (new \Exception("This order is not suitable for payment."));
        $array["order"] = $order;

        $array["paidAmount"] = $order->getPrice();
        $paymentDTO = $this->serializer->deserialize(json_encode($array), PaymentDTO::class, 'json');

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
