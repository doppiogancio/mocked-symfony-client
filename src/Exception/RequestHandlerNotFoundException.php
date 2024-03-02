<?php

namespace DoppioGancio\MockedSymfonyClient\Exception;

class RequestHandlerNotFoundException extends \Exception
{
    public function __construct(string $method, string $url)
    {
        parent::__construct(sprintf('Request Handler not found: %s %s', $method, $url));
    }
}
