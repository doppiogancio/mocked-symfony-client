<?php

namespace DoppioGancio\MockedSymfonyClient\Request\Handler;

use DoppioGancio\MockedSymfonyClient\Response\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TextRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly string $text,
        private readonly int    $status = 200,
        private array           $headers = [],
    )
    {
        $this->headers['Content-Type'] = 'text/plain';
    }

    public function __invoke(string $method, string $url, array $options): ResponseInterface
    {
        return new Response(
            content: $this->text,
            contentAsArray: [$this->text],
            status: $this->status,
            headers: $this->headers
        );
    }
}