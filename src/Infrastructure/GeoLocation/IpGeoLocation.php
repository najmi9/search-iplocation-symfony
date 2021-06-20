<?php

declare(strict_types=1);

namespace App\Infrastructure\GeoLocation;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class IpGeoLocation implements LocationInterface
{
    private HttpClientInterface $http;
    private string $apiKey;

    public function __construct(HttpClientInterface $http, string $apiKey)
    {
        $this->http = $http;
        $this->apiKey = $apiKey;
    }

    public function location(string $ip): array
    {
        return $this->http->request('GET', "https://api.ipgeolocation.io/ipgeo?apiKey={$this->apiKey}&ip={$ip}")->toArray();
    }
}