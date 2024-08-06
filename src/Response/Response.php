<?php

namespace DoppioGancio\MockedSymfonyClient\Response;

use DoppioGancio\MockedSymfonyClient\Request\Request;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Contracts\HttpClient\ResponseInterface;

class Response implements ResponseInterface
{
    private ?Request $request = null;

    public function __construct(
        private readonly string $content,
        private readonly array  $contentAsArray,
        private readonly int    $status = 200,
        private readonly array  $headers = [],
    )
    {
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getHeaders(bool $throw = true): array
    {
        if ($throw) {
            $this->checkStatusCode();
        }

        return $this->headers;
    }

    public function getContent(bool $throw = true): string
    {
        if ($throw) {
            $this->checkStatusCode();
        }

        return $this->content;
    }

    public function toArray(bool $throw = true): array
    {
        if ($throw) {
            $this->checkStatusCode();
        }

        return $this->contentAsArray;
    }

    public function cancel(): void
    {
        // TODO: Implement cancel() method.
    }

    public function getInfo(string $type = null)
    {
        $info = [
            'http_code' => $this->status,
            'url' => (string) $this->request?->getUri(),
            'response_headers' => $this->headers,
        ];

        return $type ? $info[$type] : $info;
    }

    private function checkStatusCode(): void
    {
        $code = $this->getInfo('http_code');

        if (500 <= $code) {
            throw new ServerException($this);
        }

        if (400 <= $code) {
            throw new ClientException($this);
        }

        if (300 <= $code) {
            throw new RedirectionException($this);
        }
    }
}
