<?php

namespace App\Tests\Feature;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group ItemHandleTest
 */
class FeatureControllerTest extends WebTestCase
{
    protected const ROUTE = "/api/feature/";

    public function testDefine()
    {
        $client = static::createClient();

        $body = [
            'features' => [
                'color',
                'size',
                'ram'
            ]
        ];

        $client->request(
            'POST',
            self::ROUTE.'define',
            [],
            [],
            [],
            json_encode($body)
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals('Features have been added!', $data['message']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdate()
    {
        $client = static::createClient();

        $body = [
            'status' => 0,
            'label' => 'THISNEWCOLOR'
        ];

        $client->request(
            'POST',
            self::ROUTE.'update/1',
            [],
            [],
            [],
            json_encode($body)
        );

        //Valid Data
        $response = $client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals(false, $data['status']);
        $this->assertEquals(200, $response->getStatusCode());

        $body = [
            'status' => 1
        ];

        $client->request(
            'POST',
            self::ROUTE.'update/1',
            [],
            [],
            [],
            json_encode($body)
        );

        //InValid Data
        $response = $client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals("Undefined array key \"label\"", $data);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testRead()
    {
        $client = static::createClient();

        //Valid Id
        $client->request(
            'GET',
            self::ROUTE . 'read/1'
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(false, $data['status']);
        $this->assertEquals(200, $response->getStatusCode());

        //Invalid Id
        $client->request(
            'GET',
            self::ROUTE . 'read/1000000'
        );

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testDelete()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            self::ROUTE . 'delete/2'
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
}
