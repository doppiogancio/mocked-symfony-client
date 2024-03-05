<?php

namespace DoppioGancio\MockedSymfonyClient\Request\Handler;

interface RequestHandlerInterface
{
    public function __invoke(string $method, string $url, array $options);
}
