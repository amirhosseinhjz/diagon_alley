<?php

namespace App\Service\Payment;

use App\DTO\Payment\PaymentDTO;

use nusoap_client;

class SamanPortalService extends BankPortalService
{
    public function setInitial()
    {
        $this->terminalId = 'kBkvJ7sq-zH8Z7r';
    }

    public function getToken(PaymentDTO $paymentDTO)
    {
        $data = [
            'TermID' => $this->terminalId,
            'Amounts' => $paymentDTO->paidAmount,
            'ResNum' => $paymentDTO->cart->getId() . ":" . $paymentDTO->type,
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
            "redirect_url" => "http://localhost:70/api/payment/getStatus",
        ];
    }

    public function changeStatus($result, $repository)
    {
        $payment = $repository->findOneById($result['ResNum']);

        if ($result["State"] == "OK") {
            $payment->setStatus("SUCCESS");
            $this->cartManager->updateStatus($payment->getCart()->getId(), "SUCCESS");
        } else {
            $payment->setStatus("FAILED");
            $payment->getCart()->setStatus("INIT");
        }

        $payment->setCode($result['TraceNo']);
        $repository->flush();

        return ["Id"=>$payment->getCart()->getId(), "Status"=>$result["State"]];
    }
}
