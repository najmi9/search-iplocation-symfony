<?php

namespace App\Infrastructure\Search;

interface IndexerInterface
{
    /**
     * Index a collection.
     *
     * @param string $collection the collection name(like a table in relational database)
     * @param array $data the data to be indexed
     * @param array $fields the fields of a collection, ex: [['name' => 'fieldName', 'type' => 'fieldType'],]
     * @param array{default_sorting_field: string} $options
     */
    public function index(string $collection, array $data, array $fields, array $options = []): void;

    /**
     * Delete the content of the index.
     *
     * @param string $collection
     * @param string $id
     */
    public function remove(string $collection, string $id): void;

    /**
     * Drop a collection
     *
     * @param string $collection
     */
    public function clean(string $collection): void;
}
