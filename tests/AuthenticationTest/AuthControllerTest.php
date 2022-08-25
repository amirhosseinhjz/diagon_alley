<?php

namespace App\Tests\AuthenticationTest;

use ApiTestCase\JsonApiTestCase;


/**
 * @group auth
 */
class AuthControllerTest extends JsonApiTestCase
{
    protected array $userCritics = ['username'=>'09128464488' ,'password'=>'123456789'];

    public function testUserRegister()
    {

        $body = [
            'name'=>'jery',
            'email'=>'cacvoaasfdsaqsgweo@gmail.com',
            'lastName'=>'coca',
            'phoneNumber'=>'09182836949',
            'password'=>'123456789',
            'roles'=>['ROLE_SELLER']
        ];

        $this->client->request(
            'POST',
            'http://localhost:70/api/user/register',
            [],
            [],
            [],
            json_encode($body)
        );

        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response,true);

        self::assertArrayHasKey('token',$data);
        self::assertArrayHasKey('refresh_token',$data);
    }

    public function testUserLogin()
    {
        $this->client->request(
            'POST',
            'http://localhost:70/api/user/login',
            [],
            [],
            [],
            json_encode($this->userCritics)
        );

        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response,true);

        self::assertArrayHasKey('token',$data);
        self::assertArrayHasKey('refresh_token',$data);

        $wrongPassword = '12345789';
        $this->client->request(
            'POST',
            'http://localhost:70/api/user/login',
            [],
            [],
            [],
            json_encode(['username'=>$this->userCritics['username'],'password'=>$wrongPassword])
        );

        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response,true);

        self::assertSame($data,"\"Invalid password\"");
    }

}