<?php

namespace App\Tests\Feature;

use App\Tests\Base\BaseJsonApiTestCase;

/**
 * @group FeatureTest
 */
class FeatureControllerTest extends BaseJsonApiTestCase
{
    protected const ROUTE = "/api/feature" , STATUS = 'active' , NAME = 'label';

    public function testDefine()
    {
        $response = $this->loginDefaultAdminGetToken();

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
            self::ROUTE,
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
        $response = $this->loginDefaultAdminGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $body = [
            'active' => 0,
            'label' => 'THISNEWCOLOR'
        ];

        $this->client->request(
            'PATCH',
            self::ROUTE.'/1',
            [],
            [],
            [],
            json_encode($body)
        );

        //Valid Data
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals(false, $data[self::STATUS]);
        $this->assertEquals(200, $response->getStatusCode());

        $body = [
            'active' => 1
        ];

        $this->client->request(
            'PATCH',
            self::ROUTE.'/1',
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
        $response = $this->loginDefaultAdminGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        //Valid Id
        $this->client->request(
            'GET',
            self::ROUTE . '/3'
        );

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Color2",$data['label']);

        //Invalid Id
        $this->client->request(
            'GET',
            self::ROUTE . '/1000000'
        );

        $response = $this->client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testDelete()
    {
        $response = $this->loginDefaultAdminGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $this->client->request(
            'DELETE',
            self::ROUTE . '/2'
        );

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
}