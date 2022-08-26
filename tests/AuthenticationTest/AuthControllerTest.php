<?php

namespace App\Tests\AuthenticationTest;

use ApiTestCase\JsonApiTestCase;


/**
 * @group auth
 */
class AuthControllerTest extends JsonApiTestCase
{

    protected array $defaultUser = ['username'=>'09128464485' ,'password'=>'123456789'];

//    public function testUserRegister()
//    {
//
//        $body = [
//            'name'=>'jery',
//            'email'=>'cacvoaasfdsaqsgweo@gmail.com',
//            'lastName'=>'coca',
//            'phoneNumber'=>'09182836949',
//            'password'=>'123456789',
//            'roles'=>['ROLE_SELLER']
//        ];
//
//        $this->client->request(
//            'POST',
//            'http://localhost:70/api/user/register',
//            [],
//            [],
//            [],
//            json_encode($body)
//        );
//
//        $response = $this->client->getResponse()->getContent();
//
//        $data = json_decode($response,true);
//
//        self::assertArrayHasKey('token',$data);
//
//        self::assertArrayHasKey('refresh_token',$data);
//    }

    public function testUserLogin()
    {
        $response = $this->loginDefaultUserGetToken();

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
            json_encode(['username'=>$this->defaultUser['username'],'password'=>$wrongPassword])
        );

        $response = $this->client->getResponse()->getContent();

        $data = json_decode($response,true);

        self::assertSame($data,"Invalid password");
    }

    public function testUserLogout()
    {

        $response = $this->loginDefaultUserGetToken();

        $auth = json_decode($response,true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $auth['token']));

        $this->client->request(
            'GET',
            'http://localhost:70/api/user/logout',
            [],
            [],
            [],
            ''
        );


        $response = $this->client->getResponse()->getContent();

        $data = json_decode($response,true);

        self::assertArrayHasKey('message',$data);
        self::assertSame($data['message'],"you logged out");
        self::assertSame($data['status'],200);
    }

    /**
     * @return false|string
     */
    public function loginDefaultUserGetToken(): string|false
    {
        $this->client->request(
            'POST',
            'http://localhost:70/api/user/login',
            [],
            [],
            [],
            json_encode($this->defaultUser)
        );

        $response = $this->client->getResponse()->getContent();

        return $response;
    }

}