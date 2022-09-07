<?php

namespace App\Tests\Brand;

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
            'name' => 'myName',
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

    }

    public function testDeleteBrand()
    {

    }

    public function testGetAllBrands()
    {

    }

    public function testGetOneBrand()
    {

    }

    public function testSearchBrand()
    {

    }
}