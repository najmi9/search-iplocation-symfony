<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Elasticsearch;

use App\Infrastructure\Search\IndexerInterface;
use JoliCode\Elastically\Client;
use JoliCode\Elastically\Messenger\IndexationRequest;
use JoliCode\Elastically\Messenger\IndexationRequestHandler;
use Symfony\Component\Messenger\MessageBusInterface;

class ElasticSearchIndexer implements IndexerInterface
{
    private MessageBusInterface $bus;
    private Client $client;

    public function __construct(Client $client, MessageBusInterface $bus)
    {
        $this->client = $client;
        $this->bus = $bus;
    }

    public function index(string $collection, array $data, array $fields, array $options = []): void
    {
        $this->bus->dispatch(new IndexationRequest($collection, $data['id']));
    }

    public function remove(string $collection, string $id): void
    {
        // delete a document with the $id from index ĉollection
        $this->bus->dispatch(new IndexationRequest($collection, (string) $id, IndexationRequestHandler::OP_DELETE));
    }

    public function clean(string $collection): void
    {
        $this->client->getIndex($collection)->delete();
    }
}