<?php

declare(strict_types=1);

namespace App\Infrastructure\GeoLocation;

interface LocationInterface
{
    /**
     * Get User Location based on here IP address
     *
     * @param string $ip the public user ip address
     * @return array
     */
    public function location(string $ip): array;
}