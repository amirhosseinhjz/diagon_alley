<?php

namespace App\Tests\Category;

use App\Tests\Base\BaseJsonApiTestCase;

/**
 * @group category
 */
class CategoryControllerTest extends BaseJsonApiTestCase
{
    protected const ROUTE = "/api/category/";

    public function testCreateMainCategory()
    {
//        $body = [
//            'name' => 'myCategory',
//            //TODO: parent leaf type | create separate tests for leaf and non leaf
//        ];
//        $this->client->request('POST', self::ROUTE, content: json_encode($body));
//        $response = $this->client->getResponse();
//        self::assertResponseIsSuccessful($response->getStatusCode());
//        $data = json_decode($response->getContent(), true);
//        self::assertEquals($body['name'], $data['name']);
    }

    public function testCreateLeafCategory()
    {
    }

    public function testUpdateCategory()
    {
        $body = [];
    }

    public function testDeleteCategory()
    {

    }

    public function testGetMainCategories()
    {
        //TODO needs data fixtures
        $this->client->request('GET', self::ROUTE, parameters: []);
        $response = $this->client->getResponse();
        self::assertResponseIsSuccessful($response->getStatusCode());
    }

    public function testGetOneCategory()
    {
        //TODO add categoryID, needs data fixtures
//        $this->client->request('GET', self::ROUTE, parameters: []);
//        $response = $this->client->getResponse();
//        self::assertResponseIsSuccessful($response->getStatusCode());
    }

    public function testToggleActivity()
    {
        $body = [];
    }

    public function testAddFeature()
    {
        $body = [];
    }

    public function testRemoveFeature()
    {
        $body = [];
    }

    public function testGetCategoryParents()
    {

    }

    public function testGetCategoryBrands()
    {

    }

    public function testGetCategoryFeatures()
    {

    }
}