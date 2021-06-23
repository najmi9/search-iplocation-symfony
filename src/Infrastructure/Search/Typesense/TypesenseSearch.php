<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Typesense;

use App\Infrastructure\Search\Model\Store;
use App\Infrastructure\Search\SearchInterface;
use App\Infrastructure\Search\SearchResult;
use Symfony\Component\Serializer\SerializerInterface;

class TypesenseSearch implements SearchInterface
{
    private TypesenseClient $client;
    private SerializerInterface $serializer;

    public function __construct(TypesenseClient $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    public function search(string $collection, string $q, array $options = [], int $limit = 50, int $page = 1): SearchResult
    {
        $params = [];

        if (!empty($options['highlight_full_fields'])) {
            $params['highlight_full_fields'] = \implode(',', $options['highlight_full_fields']);
        }

        if (!empty($options['range'])) {
            $field = $options['range']['field'];

            if (!empty($options['range']['min'])) {
                $min = $options['range']['min'];
                $params['filter_by'] = "{$field}:>{$min}";
            }

            if (!empty($options['range']['max'])) {
                $max = $options['range']['max'];
                if (empty($params['filter_by'])) {
                    $params['filter_by'] = "{$field}:<{$max}";
                } else {
                    $params['filter_by'] .= " && {$field}:<{$max}";
                }
            }
        }

        if (!empty($options['search_in'])) {
            $params['query_by'] = \implode(',', $options['search_in']);
        }

        $query = array_merge( [
            'q' => $q,
            'page' => $page,
            'highlight_affix_num_tokens' => 4,
            'per_page' => $limit,
            'num_typos' => 1,
        ], $params);

        ['found' => $found, 'hits' => $items] = $this->client->get("collections/{$collection}/documents/search", $query);

        $model = \ucfirst($collection);

        $type = "\\App\\Infrastructure\\Search\\Model\\{$model}";

        return new SearchResult(array_map(fn (array $item) => $this->model($item, $type), $items), $found > 10 * $limit ? 10 * $limit : $found);
    }

    public function model(array $item, string $type)
    {
        $model = $this->serializer->deserialize(json_encode($item['document']), $type, 'json');

        if (!empty($item['document']['store'])) {
            $store = new Store();
            $store->setName($item['document']['store'][1]);
            $store->setId($item['document']['store'][0]);
            $location = explode(',', $item['document']['store'][2]);

            $store->setLocation([(float) $location[0], (float) $location[1]]);

            $model->setStore($store);
        }

        return $model;
    }
}
