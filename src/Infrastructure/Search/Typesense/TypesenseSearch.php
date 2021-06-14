<?php

namespace App\Infrastructure\Search\Typesense;

use App\Infrastructure\Search\Item\TypesenseProductItem;
use App\Infrastructure\Search\Item\TypesenseUserItem;
use App\Infrastructure\Search\SearchInterface;
use App\Infrastructure\Search\SearchResult;

class TypesenseSearch implements SearchInterface
{
    private TypesenseClient $client;

    public function __construct(TypesenseClient $client)
    {
        $this->client = $client;
    }

    public function search(string $collection, string $q, array $options = [], int $limit = 50, int $page = 1): SearchResult
    {
        $query = array_merge( [
            'q' => $q,
            'page' => $page,
            'highlight_affix_num_tokens' => 4,
            'per_page' => $limit,
            'num_typos' => 1,
        ], $options);

        ['found' => $found, 'hits' => $items] = $this->client->get("collections/{$collection}/documents/search", $query);

        if ('users' === $collection) {
            return new SearchResult(array_map(fn (array $item) => new TypesenseUserItem($item), $items), $found > 10 * $limit ? 10 * $limit : $found);
        }

        return new SearchResult(array_map(fn (array $item) => new TypesenseProductItem($item), $items), $found > 10 * $limit ? 10 * $limit : $found);
    }
}
