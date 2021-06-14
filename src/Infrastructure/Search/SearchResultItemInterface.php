<?php

declare(strict_types=1);

namespace App\Infrastructure\Search;

interface SearchResultItemInterface
{
    public function getName(): string;

    public function getUrl(): string;
}