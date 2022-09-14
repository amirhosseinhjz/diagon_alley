<?php

namespace App\Service\Portal;

use App\DTO\Portal\PortalDTO;
use App\Entity\Payment\Payment;
use App\Interface\Portal\portalInterface;
use App\Service\OrderService\OrderService;
use Doctrine\ORM\EntityManagerInterface;

use nusoap_client;

class SamanPortalService implements portalInterface
{
    public const terminalId = 'kBkvJ7sq-zH8Z7r';

    public function __construct(
        private EntityManagerInterface $em,
        private OrderService $orderService,
    ) {
    }

    public function payOrder(PortalDTO $portalDTO)
    {
        $token = $this->getToken($portalDTO);

        return $this->directToPayment($token);
    }

    public function getToken(PortalDTO $portalDTO)
    {
        $data = [
            'TermID' => self::terminalId,
            'Amounts' => $portalDTO->payment->getPaidAmount(),
            'ResNum' => $portalDTO->payment->getId() . ":" .  $portalDTO->type,
        ];

        $client = new nusoap_client('https://old.banktest.ir/gateway/saman/Payments/InitPayment?wsdl', 'wsdl');
        $token = $client->call('RequestMultiSettleTypeToken', $data);

        return $token;
    }

    public function directToPayment($token)
    {
        return [
            'url' => "https://old.banktest.ir/gateway/saman/gate",
            "token" => $token,
            "redirect_url" => "http://localhost:70/api/payment/status",
        ];
    }

    public function changeStatus($result)
    {
        $payment = $this->em->getRepository(Payment::class)->find($result['ResNum']);

        if ($result["State"] == "OK") {
            $payment->setStatus("SUCCESS");
            if(is_null($payment->getPurchase()))
            {
                $payment->getWallet()->withdraw($payment->getPaidAmount());
                $this->em->flush();
            }
            else
                $this->orderService->finalizeOrder($payment->getPurchase());
        } else {
            $payment->setStatus("FAILED");
        }

        $payment->getPortal()->setCode($result['TraceNo']);
        $this->em->flush();

        if(is_null($payment->getPurchase()))
            return ["type" => "chargeWallet", "Id" => $payment->getWallet()->getId(), "Status" => $result["State"]];
        else
            return ["type" => "payOrder", "Id" => $payment->getPurchase()->getId(), "Status" => $result["State"]];
    }
}
