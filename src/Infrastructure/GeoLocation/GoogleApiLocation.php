<?php

declare(strict_types=1);

namespace App\Infrastructure\GeoLocation;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleApiLocation implements LocationInterface
{
    private HttpClientInterface $http;

    public function __construct(HttpClientInterface $http)
    {
        $this->http = $http;
    }

    public function location(string $ip): array
    {
        return [];
    }
}
