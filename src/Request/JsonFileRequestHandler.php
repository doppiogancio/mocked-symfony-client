<?php

namespace DoppioGancio\MockedSymfonyClient\Request;

use DoppioGancio\MockedSymfonyClient\Response\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class JsonFileRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly string $filename,
        private readonly int $status = 200,
        private array $headers = [],
    ) {
        $this->headers['Content-Type'] = 'application/json';
    }

    public function __invoke(string $method, string $url, array $options): ResponseInterface
    {
        $content = file_get_contents($this->filename);
        return new Response(
            content: $content,
            contentAsArray: json_decode($content, true),
            status: $this->status,
            headers: $this->headers
        );
    }
}