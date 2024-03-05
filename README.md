# Mocked Symfony Client

[![Packagist Version](https://img.shields.io/packagist/v/doppiogancio/mocked-symfony-client)](https://packagist.org/packages/doppiogancio/mocked-symfony-client)
[![Packagist Downloads](https://img.shields.io/packagist/dm/doppiogancio/mocked-symfony-client)](https://packagist.org/packages/doppiogancio/mocked-symfony-client)

# Mocked Client

This package will help test components that depend on Symfony HTTP clients for HTTP calls. At the moment only Guzzle
Client is supported.

## Install

Via Composer

```shell
$ composer require doppiogancio/mocked-symfony-client
```

## Requirements

This version requires a minimum PHP version 8.1

## How to mock a client

My advice is to create one `injectClient` private method in a TestCase class that will handler every request
of a single client. If you have multiple clients, you could use of single mocked client instance, but it would be better
to one mocked client for each client used in the code.

```php
<?php

namespace DoppioGancio\MockedSymfonyClient\Tests;

use DoppioGancio\MockedSymfonyClient\MockedClient;
use PHPUnit\Framework\TestCase;

class RealExampleTest extends TestCase
{
    private MockedClient $jsonPlaceHolderClient;
    private MockedClient $dummyJsonClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->injectJsonPlaceHolderClient();
        $this->injectDummyJsonClient();
    }
    
    public function testGetUserByJsonPlaceHolderApi(): void
    {
        $response = $this->jsonPlaceHolderClient->request('GET', '/user/1');
        self::assertEquals(200, $response->getStatusCode());

        $user = $response->toArray();
        self::assertEquals('Leanne Graham', $user['name']);
    }

    private function injectJsonPlaceHolderClient(): void
    {
        $client = new MockedClient([
            'base_uri' => [
                'https://jsonplaceholder.typicode.com',
            ],
        ]);

        $this->jsonPlaceHolderClient = $client;
    }

    private function injectDummyJsonClient(): void
    {
        $client = new MockedClient([
            'base_uri' => [
                'https://dummyjson.com',
            ],
        ]);

        $this->dummyJsonClient = $client;
    }
}
```

If you run the test now will fail because we did not yet mock any response. The good part is that
the exception will suggest us which request to mock.

```
RequestHandlerNotFoundException: Request Handler not found: GET /user/1
```

### How to mock a request/response

I first create a fixture json file with the expected content and then I mock the request.

```angular2html
[...]

use DoppioGancio\MockedSymfonyClient\Request\Handler;

[...]

$client->addRequestHandler(
    method: 'GET',
    url: '/country/it',
    requestHandler: new Handler\JsonFileRequestHandler(
        filename: __DIR__ . '/fixtures/jsonplaceholder/user_1.json'
    )
);
```

### Other request handlers

1. Handler\ArrayRequestHandler::class,
2. Handler\CallbackRequestHandler::class,
3. Handler\ConsecutiveCallsRequestHandler::class,
4. Handler\TextFileRequestHandler::class,
5. Handler\TextRequestHandler::class

## How to use the client

```php
$response = $this->jsonPlaceHolderClient->request('GET', '/user/1');
$user = $response->toArray();

// will return
array:8 [
  "id" => 1
  "name" => "Leanne Graham"
  "username" => "Bret"
  "email" => "Sincere@april.biz"
  "address" => array:5 [
    "street" => "Kulas Light"
    "suite" => "Apt. 556"
    "city" => "Gwenborough"
    "zipcode" => "92998-3874"
    "geo" => array:2 [
      "lat" => "-37.3159"
      "lng" => "81.1496"
    ]
  ]
  "phone" => "1-770-736-8031 x56442"
  "website" => "hildegard.org"
  "company" => array:3 [
    "name" => "Romaguera-Crona"
    "catchPhrase" => "Multi-layered client-server neural-net"
    "bs" => "harness real-time e-markets"
  ]
]
```

### Inject the client in the service container

If you have a service container, add the client to it, so that every service depending on it will be able to auto wire.

```php
self::$container->set(Client::class, $client);
self::$container->set('my_named_client', $client);
```
