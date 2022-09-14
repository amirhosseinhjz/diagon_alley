<?php

namespace App\Tests\Feature;

use App\Tests\Base\BaseJsonApiTestCase;

/**
 * @group FeatureTest
 */
class FeatureValueControllerTest extends BaseJsonApiTestCase
{
    protected const ROUTE = "/api/feature-value" , VALUE = 'value';

    public function testDefine()
    {
        $response = $this->loginDefaultAdminGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $body = [
            4 => 'RED',
            5 => 'BLUE',
            6 => 'YELLOW'
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
        $this->assertEquals(200, $response->getStatusCode());

        //Invalid body
        $body = [
            2 => 'gr',
            1235 => 'oppk'
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
        $this->assertEquals('"Invalid Feature ID"',$response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testDelete()
    {
        $response = $this->loginDefaultAdminGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        //Valid Id
        $this->client->request(
            'DELETE',
            self::ROUTE . '/1'
        );

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals("Feature Value deleted successfully", $data['message']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdate()
    {
        $response = $this->loginDefaultAdminGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $body = [
            'value' => 'QWE'
        ];

        $this->client->request(
            'PATCH',
            self::ROUTE.'/3',
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
        $this->assertEquals('QWE', $data[self::VALUE]);
        $this->assertEquals(200, $response->getStatusCode());

        //Invalid Id
        $this->client->request(
            'GET',
            self::ROUTE . '/1000000'
        );

        $response = $this->client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }
}