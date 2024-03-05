<?php

namespace DoppioGancio\MockedSymfonyClient\Request\Handler;

use ArrayIterator;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ConsecutiveCallsRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ArrayIterator<RequestHandlerInterface>
     */
    private ArrayIterator $handlers;

    /**
     * @param RequestHandlerInterface[] $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = new ArrayIterator($handlers);
    }

    public function __invoke(string $method, string $url, array $options): ResponseInterface
    {
        $h = $this->handlers->current();
        $this->handlers->next();

        $next = $this->handlers->current();
        if ($next == null) {
            $this->handlers->rewind();
        }

        return $h($method, $url, $options);
    }
}