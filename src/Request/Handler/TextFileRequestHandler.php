<?php

namespace DoppioGancio\MockedSymfonyClient\Request\Handler;

use DoppioGancio\MockedSymfonyClient\Response\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TextFileRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly string $filename,
        private readonly int    $status = 200,
        private array           $headers = [],
    )
    {
        $this->headers['Content-Type'] = 'text/plain';
    }

    public function __invoke(string $method, string $url, array $options): ResponseInterface
    {
        $content = file_get_contents($this->filename);
        return new Response(
            content: $content,
            contentAsArray: [$content],
            status: $this->status,
            headers: $this->headers
        );
    }
}