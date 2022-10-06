<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Entity\Task;
use Symfony\Component\HttpFoundation\Response;

class TaskApiTest extends AbstractWebTestCase
{
    public function testCreateAndUpdateTaskAuthenticatedUser()
    {
        $client = self::createClient();
        $token = $this->getBearerToken($client);

        $nameTask = uniqid('levuro-test');

        // check create task
        $client->request(
            'POST',
            '/api/tasks',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => "{$token}",
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json'
            ],
            json_encode(['name' => $nameTask])
        );

        $response = $client->getResponse();
        $this->assertTrue(Response::HTTP_CREATED === $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->assertTrue(Task::STATUS_TODO === $content['status']);
        $this->assertTrue($nameTask === $content['name']);
        $this->assertTrue(is_numeric($content['id']));

        $idTask = $content['id'];

        $name2Task = uniqid('levuro-test');

        // check update task
        $client->request(
            'PATCH',
            sprintf('/api/tasks/%d', $content['id']),
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => "{$token}",
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json'
            ],
            json_encode(['name' => $name2Task, 'status' => Task::STATUS_DONE])
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertTrue(Response::HTTP_OK === $response->getStatusCode());
        $this->assertTrue($idTask == $content['id']);
        $this->assertTrue($name2Task == $content['name']);
        $this->assertTrue(Task::STATUS_DONE == $content['status']);

        // check that we special field are required
        $client->request(
            'PATCH',
            sprintf('/api/tasks/%d', $content['id']),
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => "{$token}",
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json'
            ],
            json_encode(['name' => $name2Task, 'status' => 'status-impossible'])
        );
        $response = $client->getResponse();
        $this->assertTrue(Response::HTTP_UNPROCESSABLE_ENTITY === $response->getStatusCode());
    }

    public function testUpdateTaskNoneAuthenticatedUser()
    {
        $client = self::createClient();

        $client->request('PATCH',
            '/api/tasks/1',
            [],
            [], [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json'
            ])
        ;

        $response = $client->getResponse();
        $this->assertTrue(Response::HTTP_NOT_FOUND === $response->getStatusCode());
    }

    public function testCreateTaskNoneAuthenticatedUser()
    {
        $client = self::createClient();

        $client->request('POST',
            '/api/tasks',
            [],
            [], [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json'
            ])
        ;

        $response = $client->getResponse();
        $this->assertTrue(Response::HTTP_UNAUTHORIZED === $response->getStatusCode());
    }

    public function testGetAllTasksNoneAuthenticatedUser()
    {
        $client = self::createClient();

        $client->request('GET',
            '/api/tasks',
            [],
            [], [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json'
            ])
        ;

        $response = $client->getResponse();
        $this->assertTrue(Response::HTTP_UNAUTHORIZED === $response->getStatusCode());
    }

    public function testGetAllTasksForAuthenticatedUser()
    {
        $client = static::createClient();
        $token = $this->getBearerToken($client);

        $client->request('GET',
            '/api/tasks',
            [],
            [], [
            'HTTP_AUTHORIZATION' => "{$token}",
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json'
        ]);
        $response = $client->getResponse();
        $this->assertTrue(Response::HTTP_OK === $response->getStatusCode());
    }
}