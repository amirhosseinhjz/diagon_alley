<?php

namespace App\Tests\Variant;

use App\Tests\Base\BaseJsonApiTestCase;

/**
 * @group ItemHandleTest
 */
class VariantControllerTest extends BaseJsonApiTestCase
{
    protected array $defaultUser = ['username'=>'09128464485' ,'password'=>'123456789'];
    protected const ROUTE = "/api/variant/";

    public function testCreate()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $body = [
            'variant' => [
                'quantity' => 40,
                'price' => 55,
                'description' => 'This is first Valid variant'
            ],
            'feature' => [
                4 => 4,
                3 => 3
            ]
        ];

        $this->client->request(
            'POST',
            self::ROUTE.'create',
            [],
            [],
            [],
            json_encode($body)
        );

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals('RED3',$data['featureValues'][0]['value']);
        $this->assertEquals('RED2',$data['featureValues'][1]['value']);
        $this->assertEquals(55,$data['price']);
        $this->assertEquals(40,$data['quantity']);
        $this->assertEquals(false,$data['status']);
        $this->assertEquals(null,$data['createdAt']);
        $this->assertEquals(200, $response->getStatusCode());

        //Invalid feature value for a feature
        $body = [
            'variant' => [
                'quantity' => 40,
                'price' => 55,
            ],
            'feature' => [
                1 => 1,
                2 => 8562
            ]
        ];

        $this->client->request(
            'POST',
            self::ROUTE.'create',
            [],
            [],
            [],
            json_encode($body)
        );
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Invalid Item feature value",$data);
    }

    public function testConfirmCreate()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $this->client->request(
            'GET',
            self::ROUTE.'create/eccbc87e4b5ce2fe28308fd9f2a7baf3/confirm'
        );
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Variant confirmed successfully",$data['message']);
    }

    public function testDenied()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $this->client->request(
            'GET',
            self::ROUTE.'create/eccbc87e4b5ce2fe28308fd9f2a7baf3/denied'
        );
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Variant denied successfully",$data['message']);
    }

    public function testRead()
    {
        $this->client->request(
            'GET',
            self::ROUTE.'read/eccbc87e4b5ce2fe28308fd9f2a7baf3'
        );
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Invalid serial number",$data);

        $this->client->request(
            'GET',
            self::ROUTE.'read/c4ca4238a0b923820dcc509a6f75849b'
        );
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals('RED1',$data['featureValues'][0]['value']);
        $this->assertEquals(5,$data['price']);
        $this->assertEquals(9,$data['quantity']);
        $this->assertEquals(true,$data['status']);
        $this->assertNotEquals(null,$data['createdAt']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdate()
    {
        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $body = [
            'price' => 653,
            'quantity' => 42
        ];

        $this->client->request(
            'POST',
            self::ROUTE.'update/c4ca4238a0b923820dcc509a6f75849b',
            [],
            [],
            [],
            json_encode($body)
        );

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals('Variant updated successfully',$data['message']);
        $this->assertEquals(200, $response->getStatusCode());

        //Invalid Update
        $body = [
            'price' => 2,
            'quantity' => -4
        ];

        $this->client->request(
            'POST',
            self::ROUTE.'update/c4ca4238a0b923820dcc509a6f75849b',
            [],
            [],
            [],
            json_encode($body)
        );
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Invalid data",$data);
    }
}
