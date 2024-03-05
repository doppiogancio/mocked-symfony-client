<?php

namespace DoppioGancio\MockedSymfonyClient\Request\Handler;

use DoppioGancio\MockedSymfonyClient\Response\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ArrayRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly array $data,
        private readonly int   $status = 200,
        private array          $headers = [],
    )
    {
        $this->headers['Content-Type'] = 'application/json';
    }

    public function __invoke(string $method, string $url, array $options): ResponseInterface
    {
        return new Response(
            content: json_encode($this->data),
            contentAsArray: $this->data,
            status: $this->status,
            headers: $this->headers
        );
    }
}