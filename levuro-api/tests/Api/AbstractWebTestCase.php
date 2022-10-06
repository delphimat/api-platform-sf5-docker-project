<?php

declare(strict_types=1);

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

Class AbstractWebTestCase extends WebTestCase
{
    protected $token = null;

    protected function getBearerToken($client): string
    {
        if (null === $this->token) {
            $this->fetchValidToken($client);
        }

        return  $this->token;
    }

    protected function fetchValidToken($client): void
    {
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

        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' =>  $userName, 'password' =>  $password])
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $this->token = sprintf('Bearer %s', $content['token']);
    }
}