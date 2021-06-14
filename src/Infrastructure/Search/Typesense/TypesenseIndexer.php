<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Typesense;

use App\Infrastructure\Search\IndexerInterface;
use Symfony\Component\HttpFoundation\Response;

class TypesenseIndexer implements IndexerInterface
{
    private TypesenseClient $client;

    public function __construct(TypesenseClient $client)
    {
        $this->client = $client;
    }

    public function index(string $collection, array $data, array $fields, array $options = []): void
    {
        try {
            $this->client->patch("collections/{$collection}/documents/{$data['id']}", $data);
        } catch (TypesenseException $exception) {
            if (Response::HTTP_NOT_FOUND === $exception->status && 'Not Found' === $exception->message) {
                $this->client->post('collections', array_merge([
                    'name' => $collection,
                    'fields' => $fields,
                ], $options));
        
                $this->client->post("collections/{$collection}/documents", $data);
            } elseif (Response::HTTP_NOT_FOUND === $exception->status) {
                $this->client->post("collections/{$collection}/documents", $data);
            } else {
                throw $exception;
            }
        }
    }

    public function remove(string $collection, string $id): void
    {
        $this->client->delete("collections/{$collection}/documents/$id");
    }

    public function clean(string $collection): void
    {
        try {
            $this->client->delete("collections/{$collection}");
        } catch (TypesenseException $e) {
        }
    }
}
