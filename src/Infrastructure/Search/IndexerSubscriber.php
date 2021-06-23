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
        $indexName = $event->getIndexName();

        $fields = SearchConstants::TYPESENSE === $event->getType()  ?  $event->getFields() : [];

        $content = $event->getContent();

        $options = $event->getOptions();

        $this->indexer->index($indexName, $content, $fields, $options);
    }

    public function removeEntity(EntityDeletedEvent $event): void
    {
        $this->indexer->remove($event->getIndexName(), $event->getEntityId());
    }

    public function updateEntity(EntityUpdatedEvent $event)
    {
        $indexName = $event->getIndexName();

        $fields = SearchConstants::TYPESENSE === $event->getType()  ?  $event->getFields() : [];

        $conent = $event->getContent();

        $options = $event->getOptions();

        $this->indexer->index($indexName, $conent, $fields, $options);
    }
}
