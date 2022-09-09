<?php

namespace App\Service\Portal;

use App\DTO\Payment\PaymentDTO;
use App\DTO\Portal\PortalDTO;
use App\Entity\Payment\Payment;
use App\Entity\Portal\Portal;
use App\Service\Payment\PaymentService;
use App\Factory\Portal\PortalFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PortalService extends PaymentService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
    ) {
    }

    public function pay(PaymentDTO $paymentDto, $array)
    {

        $portalSevice = PortalFactory::create($array["type"]);

        $portalDTO = $this->portalDtoFromArray($array);
        $this->portalEntityFromDto($portalDTO);

        $responce = $portalSevice->pay($paymentDto);

        return $responce;
    }

    public function portalDtoFromArray($array)
    {
        $payment = $this->em->getRepository(Payment::class)->find($array["payment"]);
        if (is_null($payment))
            throw (new \Exception(json_encode("This payment is not exist.")));
        $array["payment"] = $payment;

        $poratalDTO = $this->serializer->deserialize(json_encode($array), PortalDTO::class, 'json');

        $DTOErrors = $this->validate($poratalDTO);
        if (count($DTOErrors) > 0) {
            throw (new \Exception(json_encode($DTOErrors)));
        }

        return $poratalDTO;
    }

    public function portalEntityFromDto(PortalDTO $poratalDTO, $flash = true)
    {
        $newPortal = new Portal();

        foreach ($poratalDTO as $key => $value) {
            $setterName = 'set' . ucfirst($key);
            $newPortal->$setterName($value);
        }
        $this->em->persist($newPortal);

        if ($flash)
            $this->em->flush();
    }
}
