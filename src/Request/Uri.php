<?php

namespace DoppioGancio\MockedSymfonyClient\Request;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    private string $scheme;
    private string $host;
    private string $path;
    private ?int $port;
    private string $user;
    private ?string $pass;
    private string $query;
    private string $fragment;
    private string $fullUrl;

    public function __construct(string $url)
    {
        $this->fullUrl = $url;
        $info = parse_url($url);

        $this->scheme = $info['scheme'] ?? '';
        $this->host = $info['host'] ?? '';
        $this->port = $info['port'] ?? null;
        $this->user = $info['user'] ?? '';
        $this->pass = $info['pass'] ?? '';
        $this->path = $info['path'] ?? '';
        $this->query = $info['query'] ?? '';
        $this->fragment = $info['fragment'] ?? '';
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        $authority = '';

        if ($this->getUserInfo() !== '') {
            $authority = $this->getUserInfo() . '@';
        }

        $authority .= $this->getHost();

        if ($this->getPort()) {
            $authority .= ':' . $this->getPort();
        }

        return $authority;
    }

    public function getUserInfo(): string
    {
        if ($this->pass) {
            return $this->user . ':' . $this->pass;
        }

        return $this->user;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme($scheme): UriInterface
    {
        $uri = clone $this;
        $uri->scheme = $scheme;
        return $uri;
    }

    public function withUserInfo($user, $password = null): UriInterface
    {
        $uri = clone $this;
        $uri->user = $user;
        $uri->pass = $password;
        return $uri;
    }

    public function withHost($host): UriInterface
    {
        $uri = clone $this;
        $uri->host = $host;
        return $uri;
    }

    public function withPort($port): UriInterface
    {
        $uri = clone $this;
        $uri->port = $port;
        return $uri;
    }

    public function withPath($path): UriInterface
    {
        $uri = clone $this;
        $uri->path = $path;
        return $uri;
    }

    public function withQuery($query): UriInterface
    {
        $uri = clone $this;
        $uri->query = $query;
        return $uri;
    }

    public function withFragment($fragment): UriInterface
    {
        $uri = clone $this;
        $uri->fragment = $fragment;
        return $uri;
    }

    public function __toString(): string
    {
        return $this->fullUrl;
    }
}
