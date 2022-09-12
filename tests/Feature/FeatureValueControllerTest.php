<?php

namespace App\Tests\Feature;

use App\Tests\Base\BaseJsonApiTestCase;

/**
 * @group ItemHandleTest
 */
class FeatureValueControllerTest extends BaseJsonApiTestCase
{
    protected const ROUTE = "/api/feature/value/" , VALUE = 'value';

    public function testDefine()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $body = [
            4 => 'RED',
            2 => 'BLUE',
            3 => 'YELLOW'
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
        $this->assertEquals(200, $response->getStatusCode());
        //Invalid body
        $body = [
            2 => 'gr',
            1235 => 'oppk'
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
        $this->assertEquals('"Invalid Feature ID"',$response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testDelete()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        //Valid Id
        $this->client->request(
            'GET',
            self::ROUTE . 'delete/1'
        );

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals("Feature Value deleted successfully", $data['message']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdate()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $body = [
            3 => 'QWE'
        ];

        $this->client->request(
            'POST',
            self::ROUTE.'update/3',
            [],
            [],
            [],
            json_encode($body)
        );

        //Valid Data
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals("Feature Value updated successfully", $data['message']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRead()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        //Valid Id
        $this->client->request(
            'GET',
            self::ROUTE . 'read/3'
        );

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('QWE', $data[self::VALUE]);
        $this->assertEquals(200, $response->getStatusCode());

        //Invalid Id
        $this->client->request(
            'GET',
            self::ROUTE . 'read/1000000'
        );

        $response = $this->client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }
}