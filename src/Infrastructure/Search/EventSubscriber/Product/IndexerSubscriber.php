<?php

namespace App\Infrastructure\Search\EventSubscriber\Product;

use App\Events\ProductCreatedEvent;
use App\Events\ProductDeletedEvent;
use App\Events\ProductUpdatedEvent;
use App\Infrastructure\Search\IndexerInterface;
use App\Infrastructure\Search\Normalizer\ProductNormalizer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IndexerSubscriber implements EventSubscriberInterface
{
    private IndexerInterface $indexer;
    private ProductNormalizer $normalizer;

    public function __construct(IndexerInterface $indexer, ProductNormalizer $normalizer)
    {
        $this->indexer = $indexer;
        $this->normalizer = $normalizer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductUpdatedEvent::class => 'updateProduct',
            ProductCreatedEvent::class => 'indexProduct',
            ProductDeletedEvent::class => 'removeProduct',
        ];
    }

    public function indexProduct(ProductCreatedEvent $event): void
    {
        $content = $this->normalizer->normalize($event->getProduct(), 'search');
        $options = [
            'default_sorting_field' => 'created_at', // created_at should be int
        ];

        $this->indexer->index('products', $content, $this->fields(), $options);
    }

    public function removeProduct(ProductDeletedEvent $event): void
    {
        $this->indexer->remove('products', (string) $event->getProduct()->getId());
    }

    public function updateProduct(ProductUpdatedEvent $event): void
    {
        $previousData = $this->normalizer->normalize($event->getPrevious(), 'search');
        $data = $this->normalizer->normalize($event->getProduct(), 'search');
        if ($previousData !== $data) {
            $options = [
                'default_sorting_field' => 'created_at',
            ];
            $this->indexer->index('products', $data, $this->fields(), $options);
        }
    }

    private function fields(): array
    {
        return [
            ['name' => 'name', 'type' => 'string'],
            ['name' => 'description', 'type' => 'string'],
            ['name' => 'shortDescription', 'type' => 'string'],
            ['name' => 'category', 'type' => 'string'],
            ['name' => 'created_at', 'type' => 'int32'],
            ['name' => 'price', 'type' => 'float'],
            ['name' => 'url', 'type' => 'string'],
        ];
    }
}
