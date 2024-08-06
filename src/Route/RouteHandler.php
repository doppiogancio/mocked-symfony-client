<?php

namespace DoppioGancio\MockedSymfonyClient\Route;

use DoppioGancio\MockedSymfonyClient\Request\Handler\RequestHandlerInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class RouteHandler
{
    public function __construct(
        public readonly string                  $method,
        public readonly string                  $url,
        public readonly RequestHandlerInterface $handler,
        public readonly array                   $headers = [],
    ) {
    }

    public function __invoke(string $url, string $method, array $options): ResponseInterface
    {
        return ($this->handler)($url, $method, $options);
    }
}
