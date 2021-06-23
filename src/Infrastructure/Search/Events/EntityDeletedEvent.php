<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Events;

use App\Entity\Product;
use App\Infrastructure\Search\SearchConstants;

class EntityDeletedEvent
{
    private int $type;
    private string $indexName;
    private string $entityId;

    public function __construct(string $indexName, string $entityId, int $type = SearchConstants::ELASTICSEARCH)
    {
        $this->type = $type;
        $this->indexName = $indexName;
        $this->entityId = $entityId;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getIndexName(): string
    {
        return $this->indexName;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }
}