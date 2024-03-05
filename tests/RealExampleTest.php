<?php

namespace DoppioGancio\MockedSymfonyClient\Tests;

use DoppioGancio\MockedSymfonyClient\Exception\RequestHandlerNotFoundException;
use DoppioGancio\MockedSymfonyClient\MockedClient;
use DoppioGancio\MockedSymfonyClient\Request\Handler;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class RealExampleTest extends TestCase
{
    private MockedClient $jsonPlaceHolderClient;
    private MockedClient $dummyJsonClient;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws RequestHandlerNotFoundException
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetUserByJsonPlaceHolderApi(): void
    {
        $response = $this->jsonPlaceHolderClient->request('GET', '/user/1');
        self::assertEquals(200, $response->getStatusCode());

        $user = $response->toArray();
        self::assertEquals('Leanne Graham', $user['name']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws RequestHandlerNotFoundException
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetUserByDummyJsonApi(): void
    {
        $response = $this->dummyJsonClient->request('GET', '/user/1');
        self::assertEquals(200, $response->getStatusCode());

        $user = $response->toArray();

        self::assertEquals('Terry', $user['firstName']);
        self::assertEquals('Medhurst', $user['lastName']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->injectJsonPlaceHolderClient();
        $this->injectDummyJsonClient();
    }

    private function injectJsonPlaceHolderClient(): void
    {
        $client = new MockedClient([
            'base_uri' => [
                'https://jsonplaceholder.typicode.com',
            ],
        ]);

        $client->addRequestHandler(
            method: 'GET',
            url: '/user/1',
            requestHandler: new Handler\JsonFileRequestHandler(
                filename: __DIR__ . '/fixtures/jsonplaceholder/user_1.json'
            )
        );

        $this->jsonPlaceHolderClient = $client;
    }

    private function injectDummyJsonClient(): void
    {
        $client = new MockedClient([
            'base_uri' => [
                'https://dummyjson.com',
            ],
        ]);

        $client->addRequestHandler(
            method: 'GET',
            url: '/user/1',
            requestHandler: new Handler\JsonFileRequestHandler(
                filename: __DIR__ . '/fixtures/dummyjson/user_1.json'
            )
        );

        $this->dummyJsonClient = $client;
    }
}