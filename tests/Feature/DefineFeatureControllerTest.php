<?php

namespace App\Tests\Feature;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group ProductTest
 */
class DefineFeatureControllerTest extends WebTestCase
{
    protected const ROUTE = "/api/feature/value/";

    public function testDefine()
    {
        $client = static::createClient();

        $body = [
            1 => 'RED',
            2 => 'BLUE',
            3 => 'YELLOW'
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
        $this->assertEquals(200, $response->getStatusCode());
        //Invalid body
        $body = [
            1 => 'gr',
            1235 => 'oppk'
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
        $this->assertEquals('"Invalid Feature ID"',$response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testDelete()
    {
        $client = static::createClient();

        //Valid Id
        $client->request(
            'GET',
            self::ROUTE . 'delete/1'
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals("Feature Value deleted successfully", $data['message']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdate()
    {
        $client = static::createClient();

        $body = [
            1 => 'QWE'
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
        $this->assertEquals("Feature Value updated successfully", $data['message']);
        $this->assertEquals(200, $response->getStatusCode());
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
        $this->assertEquals('QWE', $data['value']);
        $this->assertEquals(200, $response->getStatusCode());

        //Invalid Id
        $client->request(
            'GET',
            self::ROUTE . 'read/1000000'
        );

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }
}
