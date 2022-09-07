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
        
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $this->client->request(
            'GET',
            "http://localhost:70/api/address/add/province",
            [],
            [],
            [],
            json_encode(["name"=>"Fars"])
        );
        
        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response, true);

        self::assertArrayHasKey('status', $data);
        self::assertArrayHasKey('message', $data);
    }

    public function testAddDuplicateProvince()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response, true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        //First,load fixtures
        $this->client->request(
            'GET',
            "http://localhost:70/api/address/add/province",
            [],
            [],
            [],
            json_encode(["name"=>"Fars"])
        );

        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response, true);

        $this->assertEquals($data, '["This name is already in use"]');
    }

    public function testAddCity()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response, true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        //First,load fixtures
        $this->client->request(
            'GET',
            "http://localhost:70/api/address/add/city",
            [],
            [],
            [],
            json_encode(["name"=>"varamin","province"=>"tehran"])
        );
        $response = $this->client->getResponse()->getContent();
  
        $data = json_decode($response, true);

        self::assertArrayHasKey('status', $data);
        self::assertArrayHasKey('message', $data);
    }

    public function testAddAddress()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response, true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $body=[
            "city"=>"Damavand",
            "postCode"=>"1234567890",
            "description"=>"infinitive street",
            "lat"=>-20,
            "lng"=>44
        ];

        //First,load fixtures
        $this->client->request(
            'GET',
            "http://localhost:70/api/address/add",
            [],
            [],
            [],
            json_encode($body)
        );
        
        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response, true);
    
        self::assertArrayHasKey('status', $data);
        self::assertArrayHasKey('message', $data);
    }
}