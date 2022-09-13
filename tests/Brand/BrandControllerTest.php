<?php

namespace App\Tests\Brand;

use App\Tests\Base\BaseJsonApiTestCase;
use App\Repository\Brand\BrandRepository;
use App\DataFixtures\BrandFixtures;

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
        $brandRepository = static::getContainer()->get(BrandRepository::class);
        $initialBrand = $brandRepository->findOneByName(BrandFixtures::UPDATE_NAME);
        $id = $initialBrand->getId();

        $body = [
            'updates' => [
                'name' => 'updatedName',
                'description' => 'updated description'
            ]
        ];

        $this->client->request('PATCH', self::ROUTE . $id, content: json_encode($body));
        $response = $this->client->getResponse();
        self::assertResponseIsSuccessful($response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        self::assertEquals($body['updates']['name'], $data['name']);
    }

    public function testDeleteBrand()
    {
        $brandRepository = static::getContainer()->get(BrandRepository::class);
        $brand = $brandRepository->findOneByName(BrandFixtures::DELETE_NAME);
        $id = $brand->getId();

        $this->client->request('DELETE', self::ROUTE . $id);
        $response = $this->client->getResponse();
        self::assertResponseIsSuccessful($response->getStatusCode());
        self::assertEquals(null, $brandRepository->findOneById($id));
    }

    public function testGetAllBrands()
    {
        $this->client->request('GET', self::ROUTE);
        $response = $this->client->getResponse();
        self::assertResponseIsSuccessful($response->getStatusCode());
    }

    public function testGetOneBrand()
    {
        $brandRepository = static::getContainer()->get(BrandRepository::class);
        $brand = $brandRepository->findOneByName(BrandFixtures::READ_NAME);
        $id = $brand->getId();

        $this->client->request('GET', self::ROUTE . $id, parameters: []);
        $response = $this->client->getResponse();
        self::assertResponseIsSuccessful($response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        self::assertEquals(BrandFixtures::READ_NAME, $data['name']);
    }
}