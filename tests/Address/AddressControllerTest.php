<?php

namespace App\Tests\Address;

use App\Tests\Base\BaseJsonApiTestCase;


/**
 * @group Address
 */
class AddressControllerTest extends BaseJsonApiTestCase
{
    public function testAddProvince()
    {
        $response = $this->loginDefaultUserGetToken();
        
        $auth = json_decode($response, true);
        
        dd($auth);  
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $response = $this->client->request(
            'GET',
            "http://localhost:70/api/payment/20/Saman",
            ["name"=>"Fars"]
        );
        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response, true);

        self::assertArrayHasKey('status', $data);
        self::assertArrayHasKey('message', $data);
    }

    public function testAddCity()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response, true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        //First,load fixtures
        $response = $this->client->request(
            'GET',
            "http://localhost:70/api/payment/20/Saman",
            ["name"=>"varamin","province"=>"tehran"]
        );

        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response, true);

        self::assertArrayHasKey('status', $data);
        self::assertArrayHasKey('message', $data);
    }

    public function testAddDuplicateCity()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response, true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        //First,load fixtures
        $response = $this->client->request(
            'GET',
            "http://localhost:70/api/payment/20/Saman",
            ["name"=>"varamin","province"=>"tehran"]
        );

        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response, true);

        dd($data);
    }
}