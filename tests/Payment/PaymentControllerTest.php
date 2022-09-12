<?php

namespace App\Tests\Payment;

use App\Tests\Base\BaseJsonApiTestCase;


/**
 * @group payment
 */
class PaymentControllerTest extends BaseJsonApiTestCase
{
    public function testCreatePaymentHasCart()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response, true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        //First,load fixtures
        $response = $this->client->request(
            'GET',
            "http://localhost:70/api/payment/1/Saman",
            [],
            [],
            [],
            ""
        );

        $response = $this->client->getResponse()->getContent();

        self::assertStringStartsWith("<!DOCTYPE html>", $response);
    }

    public function testCreatePaymentInvalidCart()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response, true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        //First,load fixtures
        $response = $this->client->request(
            'GET',
            "http://localhost:70/api/payment/20/Saman",
            [],
            [],
            [],
            ""
        );

        $response = $this->client->getResponse()->getContent();

        $this->assertEquals($response, '"This cart is not exist."');
    }

    public function testStatusPayment()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response, true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $body = [
            "State"          => "OK",
            "StateCode"      => "0",
            "ResNum"          => "1:Saman",
            "MID"             => "kBkvJ7sq-zH8Z7r",
            "RefNum"          => "ZjMyNTI3MGNmNmU4YjBmNDU3OGE4OT",
            "RRN"             => "12315280",
            "TraceNo"         => "123415280",
            "CID"             => "965d743d06aa5f79f5502bb1c903669f02ff168369f6f6566ccfdbd273e44a6a",
            "SecurePan"       => "636214******0624",
            "RedirectURL"     => "http://localhost:70/api/payment/getStatus",
            "CallbackError"   => "-",
            "VerifyError"     => "-",
            "ReverseError"    => "-",
        ];

        $response = $this->client->request(
            'POST',
            "http://localhost:70/api/payment/getStatus",
            $body,
        );

        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response, true);


        self::assertArrayHasKey('Id', $data);
        self::assertArrayHasKey('Status', $data);
    }
}
