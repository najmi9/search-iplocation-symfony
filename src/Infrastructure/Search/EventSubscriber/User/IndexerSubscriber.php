<?php

namespace App\Infrastructure\Search\EventSubscriber\User;

use App\Events\UserCreatedEvent;
use App\Infrastructure\Search\IndexerInterface;
use App\Infrastructure\Search\Normalizer\UserNormalizer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IndexerSubscriber implements EventSubscriberInterface
{
    private IndexerInterface $indexer;
    private UserNormalizer $normalizer;

    public function __construct(IndexerInterface $indexer, UserNormalizer $normalizer)
    {
        $this->indexer = $indexer;
        $this->normalizer = $normalizer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedEvent::class => 'indexUser',
        ];
    }

    public function indexProduct(UserCreatedEvent $event): void
    {
        $content = $this->normalizer->normalize($event->getProduct(), 'search');
        $options = [
            'default_sorting_field' => 'created_at', // created_at should be int
        ];

        $this->indexer->index('users', $content, $this->fields(), $options);
    }

    private function fields(): array
    {
        return [
            ['name' => 'name', 'type' => 'string'],
            ['name' => 'created_at', 'type' => 'int32'],
            ['name' => 'url', 'type' => 'string'],
        ];
    }
}
