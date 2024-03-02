<?php

namespace DoppioGancio\MockedSymfonyClient\Request;

interface RequestHandlerInterface
{
    public function __invoke(string $method, string $url, array $options);
}
