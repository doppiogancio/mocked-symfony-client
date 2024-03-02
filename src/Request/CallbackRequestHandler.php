<?php

namespace DoppioGancio\MockedSymfonyClient\Request;

use Closure;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CallbackRequestHandler implements RequestHandlerInterface
{
    public function __construct(private readonly Closure $callback) {
    }

    public function __invoke(string $method, string $url, array $options): ResponseInterface
    {
        return ($this->callback)($method, $url, $options);
    }
}