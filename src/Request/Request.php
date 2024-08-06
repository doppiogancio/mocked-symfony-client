<?php

namespace DoppioGancio\MockedSymfonyClient\Request;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
    private string $protocolVersion;
    private array $headers = [];

    private StreamInterface $body;

    private string $requestTarget;
    private string $method;
    private UriInterface $uri;

    public function __construct(string $method, string $url)
    {
        $this->method = $method;
        $this->uri = new Uri($url);
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        $request = clone $this;
        $request->protocolVersion = $version;
        return $request;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return array_key_exists($name, $this->headers);
    }

    public function getHeaderLine(string $name): string
    {
        return implode(',', $this->getHeader($name));
    }

    public function getHeader(string $name): array
    {
        return $this->headers[$name] ?? [];
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        $request = clone $this;
        if (is_string($value)) {
            $value = [$value];
        }

        $request->headers[$name] = $value;
        return $request;
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $request = clone $this;

        $values = $value;
        if (is_string($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            $request->headers[$name][] = $value;
        }

        return $request;
    }

    public function withoutHeader(string $name): MessageInterface
    {
        $request = clone $this;
        unset($request->headers[$name]);
        return $request;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        $request = clone $this;
        $request->body = $body;
        return $request;
    }

    public function getRequestTarget(): string
    {
        return $this->requestTarget;
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        $request = clone $this;
        $request->requestTarget = $requestTarget;
        return $request;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): RequestInterface
    {
        $request = clone $this;
        $request->method = $method;
        return $request;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        $host = $uri->getHost();
        if ($preserveHost) {
            $host = $this->uri->getHost();
        }

        $request = clone $this;
        $request->uri = $uri->withHost($host);
        return $request;
    }
}
