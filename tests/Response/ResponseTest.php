<?php

namespace DoppioGancio\MockedSymfonyClient\Tests\Response;

use DoppioGancio\MockedSymfonyClient\Response\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Component\HttpClient\Exception\ServerException;

class ResponseTest extends TestCase
{
    public function testResponseNotThrowingExceptions(): void
    {
        $response = $this->createResponse();
        self::assertEquals('Not Authorized!', $response->getContent(false));
    }

    public function testResponseThrowingClientExceptions(): void
    {
        $response = $this->createResponse();

        self::expectException(ClientException::class);
        self::assertEquals('Not Authorized!', $response->getContent());
    }

    public function testResponseThrowingServerExceptions(): void
    {
        $response = new Response(
            content: 'Bad gateway, I am sorry.',
            contentAsArray: [],
            status: 502,
        );

        self::expectException(ServerException::class);
        self::assertEquals('Bad gateway, I am sorry.', $response->getContent());
        self::assertEquals(502, $response->getStatusCode());
    }

    public function testResponseThrowingRedirectExceptions(): void
    {
        $response = new Response(
            content: 'The content was permanently redirected!',
            contentAsArray: [],
            status: 308,
        );

        self::expectException(RedirectionException::class);
        self::assertEquals('The content was permanently redirected!', $response->getContent());
        self::assertEquals(308, $response->getStatusCode());
    }

    /**
     * @return Response
     */
    public function createResponse(): Response
    {
        return new Response(
            content: 'Not Authorized!',
            contentAsArray: [],
            status: 401,
        );
    }
}
