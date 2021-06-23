<?php

declare(strict_types=1);

namespace App\Infrastructure\Search;

use App\Infrastructure\Search\Events\EntityCreatedEvent;
use App\Infrastructure\Search\Events\EntityDeletedEvent;
use App\Infrastructure\Search\Events\EntityUpdatedEvent;
use App\Infrastructure\Search\IndexerInterface;
use App\Infrastructure\Search\SearchConstants;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IndexerSubscriber implements EventSubscriberInterface
{
    private IndexerInterface $indexer;

    public function __construct(IndexerInterface $indexer)
    {
        $this->indexer = $indexer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EntityUpdatedEvent::class => 'updateEntity',
            EntityCreatedEvent::class => 'indexEntity',
            EntityDeletedEvent::class => 'removeEntity',
        ];
    }

    public function indexEntity(EntityCreatedEvent $event): void
    {
        $class = \ucfirst($event->getIndexName());

        $indexName = SearchConstants::TYPESENSE === $event->getType()  ? $event->getIndexName() : "App\\Infrastructure\\Search\\Model\\{$class}";

        $fields = SearchConstants::TYPESENSE === $event->getType() ?  $event->getFields() : [];

        $content = $event->getContent();

        $options = $event->getOptions();

        $this->indexer->index($indexName, $content, $fields, $options);
    }

    public function removeEntity(EntityDeletedEvent $event): void
    {
        $class = \ucfirst($event->getIndexName());

        $indexName = SearchConstants::TYPESENSE === $event->getType()  ? $event->getIndexName() : "App\\Infrastructure\\Search\\Model\\{$class}";

        $this->indexer->remove($indexName, $event->getEntityId());
    }

    public function updateEntity(EntityUpdatedEvent $event)
    {
        $class = \ucfirst($event->getIndexName());

        $indexName = SearchConstants::TYPESENSE === $event->getType()  ? $event->getIndexName() : "App\\Infrastructure\\Search\\Model\\{$class}";

        $fields = SearchConstants::TYPESENSE === $event->getType() ?  $event->getFields() : [];

        $conent = $event->getContent();

        $options = $event->getOptions();

        $this->indexer->index($indexName, $conent, $fields, $options);
    }
}
