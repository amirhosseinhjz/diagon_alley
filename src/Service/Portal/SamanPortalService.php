<?php

namespace App\Service\Portal;

use App\DTO\Payment\PaymentDTO;
use App\Entity\Portal\Portal;
use App\Interface\Portal\portalInterface;
use Doctrine\ORM\EntityManagerInterface;

use nusoap_client;

class SamanPortalService implements portalInterface
{
    public const terminalId = 'kBkvJ7sq-zH8Z7r';

    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function payOrder(PaymentDTO $paymentDTO)
    {
        $token = $this->getToken($paymentDTO);

        return $this->directToPayment($token);
    }

    public function getToken(PaymentDTO $paymentDTO)
    {
        $data = [
            'TermID' => self::terminalId,
            'Amounts' => $paymentDTO->paidAmount,
            'ResNum' => $paymentDTO->portal->getId() . ":" .  $paymentDTO->portal->getType(),
        ];

        $client = new nusoap_client('https://banktest.ir/gateway/saman/Payments/InitPayment?wsdl', 'wsdl');
        $token = $client->call('RequestMultiSettleTypeToken', $data);

        return $token;
    }

    public function directToPayment($token)
    {
        return [
            'url' => "https://banktest.ir/gateway/saman/gate",
            "token" => $token,
            "redirect_url" => "http://localhost:70/api/payment/status",
        ];
    }

    public function changeStatus($result)
    {
        $portal = $this->em->getRepository(Portal::class)->find($result['ResNum']);

        if ($result["State"] == "OK") {
            $portal->getPayment()->setStatus("SUCCESS");
            //change status of order
        } else {
            $portal->getPayment()->setStatus("FAILED");
        }

        $portal->getPayment()->setCode($result['TraceNo']);
        $this->em->flush();

        return ["Id" => $portal->getPayment()->getOrder()->getId(), "Status" => $result["State"]];
    }
}
