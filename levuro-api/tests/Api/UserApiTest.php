<?php

declare(strict_types=1);

namespace App\Tests\Api;

use Symfony\Component\HttpFoundation\Response;

class UserApiTest extends AbstractWebTestCase
{

    public function testCreateAndLogin()
    {
        $client = static::createClient();
        $userName = uniqid('username_test-');
        $password = 'password';

        // test creation
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' =>  $userName, 'password' =>  $password])
        );
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(Response::HTTP_CREATED === $response->getStatusCode());
        $this->assertTrue(isset($content['id']));

        // test login
        $client->request('POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' =>  $userName, 'password' =>  $password]));
        $response = $client->getResponse();
        $this->assertTrue(Response::HTTP_OK === $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(!empty($content));
    }

    public function testLogInToNoneExistingUserAccount()
    {
        $client = self::createClient();
        $client->request('POST', '/api/login', [
            'json' => [
                'username' => 'user1',
                'password' => 'todo'
            ]
        ]);
        $response = $client->getResponse();

        $this->assertTrue(Response::HTTP_FORBIDDEN === $response->getStatusCode());
    }

}