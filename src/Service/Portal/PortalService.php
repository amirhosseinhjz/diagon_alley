<?php

namespace App\Service\Portal;

use App\DTO\Payment\PaymentDTO;
use App\DTO\Portal\PortalDTO;
use App\Entity\Payment\Payment;
use App\Entity\Portal\Portal;
use App\Service\Payment\PaymentService;
use App\Factory\Portal\PortalFactory;

class PortalService extends PaymentService
{
    public function pay(PaymentDTO $paymentDto, $array)
    {
        $portalSevice = PortalFactory::create($array["type"],$this->em,$this->orderService);

        $portalDTO = $this->portalDtoFromArray($array);
        $this->portalEntityFromDto($portalDTO);

        $responce = $portalSevice->payOrder($portalDTO);

        return $responce;
    }

    public function portalDtoFromArray($array)
    {
        $payment = $this->em->getRepository(Payment::class)->find($array["payment"]);
        if (is_null($payment))
            throw (new \Exception(json_encode("This payment is not exist.")));
        unset($array["payment"]); 

        $portalDTO = $this->serializer->deserialize(json_encode($array), PortalDTO::class, 'json');
        $portalDTO->payment = $payment;

        $DTOErrors = $this->validate($portalDTO);
        if (count($DTOErrors) > 0) {
            throw (new \Exception(json_encode($DTOErrors)));
        }

        return $portalDTO;
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
