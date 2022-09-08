<?php

namespace App\Tests\Brand;

use App\DataFixtures\BrandFixtures;
use App\Tests\Base\BaseJsonApiTestCase;

/**
 * @group brand
 */
class BrandControllerTest extends BaseJsonApiTestCase
{
    protected const ROUTE = "/api/brand/";

    public function testCreateBrand()
    {
        $body = [
            'name' => 'myBrand',
            'description' => 'my description'
        ];
        $this->client->request('POST', self::ROUTE, content: json_encode($body));
        $response = $this->client->getResponse();
        self::assertResponseIsSuccessful($response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        self::assertEquals($body['name'], $data['name']);
    }

    public function testUpdateBrand()
    {

        $body = [];
    }

    public function testDeleteBrand()
    {

    }

    public function testGetAllBrands()
    {
        $this->client->request('GET', self::ROUTE);
        $response = $this->client->getResponse();
        self::assertResponseIsSuccessful($response->getStatusCode());
    }

    public function testGetOneBrand()
    {
        //TODO add brandID, needs data fixtures
        $this->client->request('GET', self::ROUTE, parameters: []);
        $response = $this->client->getResponse();
        self::assertResponseIsSuccessful($response->getStatusCode());
    }

    public function testSearchBrand()
    {
        //query params
    }
}