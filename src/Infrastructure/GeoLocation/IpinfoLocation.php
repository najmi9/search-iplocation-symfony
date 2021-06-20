<?php

declare(strict_types=1);

namespace App\Infrastructure\GeoLocation;

use ipinfo\ipinfo\IPinfo;

class IpinfoLocation implements LocationInterface
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function location(string $ip): array
    {
        $settings = ['cache_maxsize' => 30, 'cache_ttl' => 128];

        $client = new IPinfo($this->token, $settings);

        $details = $client->getDetails($ip);

        return $details->all;
    }
}
