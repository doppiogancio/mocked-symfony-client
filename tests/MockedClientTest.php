<?php

namespace DoppioGancio\MockedSymfonyClient;

use DoppioGancio\MockedSymfonyClient\Exception\RequestHandlerNotFoundException;
use DoppioGancio\MockedSymfonyClient\Request\ArrayRequestHandler;
use DoppioGancio\MockedSymfonyClient\Request\CallbackRequestHandler;
use DoppioGancio\MockedSymfonyClient\Request\ConsecutiveCallsRequestHandler;
use DoppioGancio\MockedSymfonyClient\Request\JsonFileRequestHandler;
use DoppioGancio\MockedSymfonyClient\Request\TextFileRequestHandler;
use DoppioGancio\MockedSymfonyClient\Request\TextRequestHandler;
use DoppioGancio\MockedSymfonyClient\Response\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MockedClientTest extends TestCase
{
    private MockedClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->injectClient();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws RequestHandlerNotFoundException
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testJsonRequestHandler(): void
    {
        $response = $this->client->request(
            'GET',
            'https://www.myapi.com/country/it'
        );

        $country = $response->toArray();

        self::assertEquals('Italy', $country['name']);
        self::assertEquals('IT', $country['iso2code']);
        self::assertEquals('ITA', $country['iso3code']);
        self::assertArrayHasKey('Content-Type', $response->getHeaders());
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws RequestHandlerNotFoundException
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testCallbackRequestHandler(): void
    {
        $country = $this->client->request(
            'GET',
            'https://www.myapi.com/country/es'
        )->toArray();

        self::assertEquals('Spain', $country['name']);
        self::assertEquals('ES', $country['iso2code']);
        self::assertEquals('ESP', $country['iso3code']);
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws RequestHandlerNotFoundException
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testOverwriteRequestHandler(): void
    {
        $handler = new CallbackRequestHandler(
            callback: function (string $method, string $url): ResponseInterface {
                $content = file_get_contents(__DIR__ . '/fixtures/country_it.json');

                return new Response(
                    content: $content,
                    contentAsArray: json_decode($content, true),
                    headers: [
                        'Api-Key' => '1234',
                        'Referrer' => $url,
                    ]
                );
            }
        );

        $this->client->addRequestHandler(
            method: 'GET',
            url: '/country/uk',
            requestHandler: $handler
        );

        $response = $this->client->request(
            'GET',
            'https://www.myapi.com/country/uk'
        );

        self::assertEquals('1234', $response->getHeaders()['Api-Key']);
        self::assertEquals(
            'https://www.myapi.com/country/uk',
            $response->getHeaders()['Referrer']
        );
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws RequestHandlerNotFoundException
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testTextRequestHandler(): void
    {
        $response = $this->client->request(
            'GET',
            'https://www.myapi.com/country/de'
        );

        self::assertEquals(404, $response->getStatusCode());
        self::assertEquals('Country not found!', $response->getContent());
    }

    /**
     * @throws RequestHandlerNotFoundException
     */
    public function testRequestHandlerNotFoundException(): void
    {
        self::expectException(RequestHandlerNotFoundException::class);

        // The exception message removes from the url the client base_uri when configured.
        self::expectExceptionMessage('Request Handler not found: GET /country/fr');

        $this->client->request(
            'GET',
            'https://www.myapi.com/country/fr'
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws RequestHandlerNotFoundException
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testConsecutiveCalls(): void
    {
        $data = [];
        foreach (range(1, 4) as $ignored) {
            $data[] = $this->client->request(
                'GET',
                'https://www.myapi.com/country/pr'
            )->toArray();
        }

        self::assertEquals([
            ['response' => '#1'],
            ['response' => '#2'],
            ['response' => '#3'],
            ['response' => '#1'],
        ], $data);
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws RequestHandlerNotFoundException
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testTextFileRequestHandler(): void
    {
        $response = $this->client->request('PUT', 'https://www.myapi.com/lorem/ipsum');
        self::assertStringStartsWith('Lorem Ipsum is simply dummy text', $response->getContent());
    }

    private function injectClient(): void
    {
        $client = new MockedClient([
            'base_uri' => [
                'https://www.myapi.com',
            ],
        ]);

        $client->addRequestHandler(
            method: 'GET',
            url: '/country/it',
            requestHandler: new JsonFileRequestHandler(filename: __DIR__ . '/fixtures/country_it.json')
        );

        $client->addRequestHandler(
            method: 'GET',
            url: '/country/de',
            requestHandler: new TextRequestHandler(text: 'Country not found!', status: 404)
        );

        $client->addRequestHandler(
            method: 'GET',
            url: '/country/pr',
            requestHandler: new ConsecutiveCallsRequestHandler(
                handlers: [
                    new ArrayRequestHandler(data: ['response' => '#1']),
                    new ArrayRequestHandler(data: ['response' => '#2']),
                    new ArrayRequestHandler(data: ['response' => '#3']),
                ]
            )
        );

        $client->addRequestHandler(
            method: 'GET',
            url: '/country/es',
            requestHandler: new CallbackRequestHandler(
                callback: function (string $method, string $url, array $options): ResponseInterface {
                    $content = file_get_contents(__DIR__ . '/fixtures/country_es.json');

                    return new Response(
                        content: $content,
                        contentAsArray: json_decode($content, true)
                    );
                }
            )
        );

        $client->addRequestHandler(
            'PUT',
            '/lorem/ipsum',
            requestHandler: new TextFileRequestHandler(filename: __DIR__ . '/fixtures/lorem_ipsum.txt')
        );

        $this->client = $client;
    }
}
