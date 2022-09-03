<?php

namespace App\Tests\Feature;

use App\Tests\Base\BaseJsonApiTestCase;

/**
 * @group ItemHandleTest
 */
class FeatureControllerTest extends BaseJsonApiTestCase
{
    protected array $defaultUser = ['username'=>'09128464485' ,'password'=>'123456789'];
    protected const ROUTE = "/api/feature/";

    public function testDefine()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $body = [
            'features' => [
                'color',
                'size',
                'ram'
            ]
        ];

        $this->client->request(
            'POST',
            self::ROUTE.'define',
            [],
            [],
            [],
            json_encode($body)
        );

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals('Features have been added!', $data['message']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdate()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $body = [
            'status' => 0,
            'label' => 'THISNEWCOLOR'
        ];

        $this->client->request(
            'POST',
            self::ROUTE.'update/1',
            [],
            [],
            [],
            json_encode($body)
        );

        //Valid Data
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals(false, $data['status']);
        $this->assertEquals(200, $response->getStatusCode());

        $body = [
            'status' => 1
        ];

        $this->client->request(
            'POST',
            self::ROUTE.'update/1',
            [],
            [],
            [],
            json_encode($body)
        );

        //InValid Data
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals("Undefined array key \"label\"", $data);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testRead()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        //Valid Id
        $this->client->request(
            'GET',
            self::ROUTE . 'read/1'
        );

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(false, $data['status']);
        $this->assertEquals(200, $response->getStatusCode());

        //Invalid Id
        $this->client->request(
            'GET',
            self::ROUTE . 'read/1000000'
        );

        $response = $this->client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testDelete()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $this->client->request(
            'GET',
            self::ROUTE . 'delete/2'
        );

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
}
