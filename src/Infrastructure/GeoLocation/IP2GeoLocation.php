<?php

declare(strict_types=1);

namespace App\Infrastructure\GeoLocation;

class IP2GeoLocation implements LocationInterface
{
    private string $root_dir;

    public function __construct(string $root_dir)
    {
        $this->root_dir = $root_dir;
    }

    public function location(string $ip): array
    {
        $db = new \IP2Location\Database ($this->root_dir.'/IP2LOCATION-LITE-DB11.IPV6.BIN', \IP2Location\Database::FILE_IO);

        return $db->lookup($ip, \IP2Location\Database::ALL);
    }
}

