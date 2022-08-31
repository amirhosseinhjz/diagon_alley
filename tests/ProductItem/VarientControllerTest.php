<?php

namespace App\Tests\ProductItem;

use App\Controller\ProductItem\VarientController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group ProductTest
 */
class VarientControllerTest extends WebTestCase
{
    protected const ROUTE = "/api/varient/";

    public function testCreate()
    {
        $client = static::createClient();

        $body = [
            'varient' => [
                'quantity' => 40,
                'price' => 55,
                'description' => 'This is first Valid variant'
            ],
            'feature' => [
                1 => 1,
                2 => 2,
                3 => 3
            ]
        ];

        $client->request(
            'POST',
            self::ROUTE.'create',
            [],
            [],
            [],
            json_encode($body)
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals('RED1',$data['itemValues'][1]['value']);
        $this->assertEquals('RED2',$data['itemValues'][2]['value']);
        $this->assertEquals(55,$data['price']);
        $this->assertEquals(40,$data['quantity']);
        $this->assertEquals(false,$data['status']);
        $this->assertEquals(null,$data['createdAt']);
        $this->assertEquals(200, $response->getStatusCode());

        //Invalid feature value for a feature
        $body = [
            'varient' => [
                'quantity' => 40,
                'price' => 55,
            ],
            'feature' => [
                1 => 1,
                2 => 8562
            ]
        ];

        $client->request(
            'POST',
            self::ROUTE.'create',
            [],
            [],
            [],
            json_encode($body)
        );
        $response = $client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Invalid Item feature value",$data);
    }

    public function testConfirmCreate()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            self::ROUTE.'create/a87ff679a2f3e71d9181a67b7542122c/confirm'
        );
        $response = $client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Varient confirmed successfully",$data['massage']);
    }

    public function testDenied()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            self::ROUTE.'create/a87ff679a2f3e71d9181a67b7542122c/denied'
        );
        $response = $client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Varient denied successfully",$data['massage']);
    }

    public function testRead()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            self::ROUTE.'read/a87ff679a2f3e71d9181a67b7542122c'
        );
        $response = $client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Invalid serial number",$data);

        $client->request(
            'GET',
            self::ROUTE.'read/c4ca4238a0b923820dcc509a6f75849b'
        );
        $response = $client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals('RED1',$data['itemValues'][0]['value']);
        $this->assertEquals(5,$data['price']);
        $this->assertEquals(9,$data['quantity']);
        $this->assertEquals(true,$data['status']);
        $this->assertNotEquals(null,$data['createdAt']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdate()
    {
        $client = static::createClient();

        $body = [
            'price' => 653,
            'quantity' => 42
        ];

        $client->request(
            'POST',
            self::ROUTE.'update/c4ca4238a0b923820dcc509a6f75849b',
            [],
            [],
            [],
            json_encode($body)
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals('Varient updated successfully',$data['massage']);
        $this->assertEquals(200, $response->getStatusCode());

        //Invalid Update
        $body = [
            'price' => 2,
            'quantity' => -4
        ];

        $client->request(
            'POST',
            self::ROUTE.'update/c4ca4238a0b923820dcc509a6f75849b',
            [],
            [],
            [],
            json_encode($body)
        );
        $response = $client->getResponse();
        $data = json_decode($response->getContent(),true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Invalid data",$data);
    }
}
