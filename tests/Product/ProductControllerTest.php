<?php

namespace App\Tests\Product;

use App\Tests\Base\BaseJsonApiTestCase;

/**
 * @group product
 */
class ProductControllerTest extends BaseJsonApiTestCase
{
    protected const ROUTE = "/api/product/";

    public function testCreateProduct()
    {
//        $body = [
//            'name' => 'myProduct',
//            //TODO: category brand description
//        ];
//        $this->client->request('POST', self::ROUTE, content: json_encode($body));
//        $response = $this->client->getResponse();
//        self::assertResponseIsSuccessful($response->getStatusCode());
//        $data = json_decode($response->getContent(), true);
//        self::assertEquals($body['name'], $data['name']);
    }

    public function testUpdateProduct()
    {
        $body = [];
    }

    public function testDeleteProduct()
    {

    }

    public function testAddFeature()
    {
        $body = [];
    }

    public function testRemoveFeature()
    {
        $body = [];
    }

    public function testToggleActivity()
    {
        $body = [];
    }

    public function testGetBrandProducts()
    {
        $body = [];
    }

    public function testGetCategoryProducts()
    {
        $body = [];
    }

    public function testGetOneProduct()
    {

    }
}