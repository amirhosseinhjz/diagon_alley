<?php

namespace App\Service\Payment;

use App\Interface\Payment\BankPortalInterface;
use App\Repository\Payment\PaymentRepository;
use App\DTO\Payment\PaymentDTO;
use App\Entity\Payment\Payment;
use App\Service\CartManager;
use nusoap_client;

class SamanPortalService extends BankPortalService
{
    public function setInitial()
    {
        $this->terminalId='kBkvJ7sq-zH8Z7r';
        $this->userName='user2366';
        $this->password='13472887';
    }

    public function getToken(PaymentDTO $paymentDTO)
    {
        $data = [
            'TermID'=> $this->terminalId,
            'Amounts' =>$paymentDTO->paidAmount,
            //TODO: change to Id
            'ResNum'=>$paymentDTO->cartId,
        ];

        $client = new nusoap_client('https://banktest.ir/gateway/saman/Payments/InitPayment?wsdl','wsdl');
        $token = $client->call('RequestMultiSettleTypeToken',$data);

        return $token;
    }

    public function directToPayment($token)
    {
        return [
            'url' => "https://banktest.ir/gateway/saman/gate",
            "token"=> $token,
            "redirect_url"=> "http://localhost:70/api/payment/getStatus",
        ];
    }
}