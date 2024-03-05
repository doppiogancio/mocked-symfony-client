<?php

namespace DoppioGancio\MockedSymfonyClient;

use DoppioGancio\MockedSymfonyClient\Exception\RequestHandlerNotFoundException;
use DoppioGancio\MockedSymfonyClient\Request\Handler\RequestHandlerInterface;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class MockedClient implements HttpClientInterface
{
    private array $handlers = [];

    public function __construct(private readonly array $options = [])
    {
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function withOptions(array $options): static
    {
        return new MockedClient($options);
    }

    public function reset(): void
    {
        $this->handlers = [];
    }

    public function addRequestHandler(string $method, string $url, RequestHandlerInterface $requestHandler): void
    {
        if (!empty($this->options['base_uri'])) {
            $url = $this->options['base_uri'][0] . $url;
        }

        $key = $this->getKey($method, $url);
        $this->handlers[$key] = $requestHandler;
    }

    /**
     * @param string $method
     * @param string $url
     * @return string
     */
    public function getKey(string $method, string $url): string
    {
        return $method . ' ' . $url;
    }

    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * @throws RequestHandlerNotFoundException
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $key = $this->getKey($method, $url);
        if (!array_key_exists($key, $this->handlers)) {
            $url = str_replace($this->options['base_uri'] ?? '', '', $url);
            throw new RequestHandlerNotFoundException($method, $url);
        }

        return $this->handlers[$key]($method, $url, $options);
    }

    /**
     * Yields responses chunk by chunk as they complete.
     *
     * @param ResponseInterface|iterable<array-key, ResponseInterface> $responses One or more responses created by the current HTTP client
     * @param float|null $timeout The idle timeout before yielding timeout chunks
     * @throws Exception
     */
    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        throw new Exception('not yet implemented!');
    }
}
