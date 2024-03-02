<?php

namespace DoppioGancio\MockedSymfonyClient\Response;

use Symfony\Contracts\HttpClient\ResponseInterface;

class Response implements ResponseInterface
{
    public function __construct(
        private readonly string $content,
        private readonly array $contentAsArray,
        private readonly int $status = 200,
        private readonly array $headers = [],
    )
    {
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getHeaders(bool $throw = true): array
    {
        return $this->headers;
    }

    public function getContent(bool $throw = true): string
    {
        return $this->content;
    }

    public function toArray(bool $throw = true): array
    {
        return $this->contentAsArray;
    }

    public function cancel(): void
    {
        // TODO: Implement cancel() method.
    }

    public function getInfo(string $type = null)
    {
        return [];
    }
}
