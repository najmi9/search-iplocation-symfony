<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Elasticsearch;

use App\Infrastructure\Search\SearchInterface;
use App\Infrastructure\Search\SearchResult;
use JoliCode\Elastically\Client;
use Symfony\Component\Serializer\SerializerInterface;

class ElasticSearch implements SearchInterface
{
    private Client $client;
    private SerializerInterface $serializer;

    public function __construct(Client $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    public function search(string $collection, string $q = '', array $options = [], int $limit = 50, int $page = 1): SearchResult
    {
        $boolQuery = new \Elastica\Query\BoolQuery();

        if ('' === $q) {
            $boolQuery->addMust(new \Elastica\Query\MatchAll());
        } else {
            $qQuery = new \Elastica\Query\MultiMatch();

            if (!empty($options['search_in'])) {
                $qQuery->setFields($options['search_in']);
            }
            $qQuery->setQuery($q);

            $boolQuery->addMust($qQuery);
        }

        // make sure that the options exists
        if (!empty($options['range'])) {
            $params = [];
            if (!empty($options['range']['min'])) {
                $params['gte'] = $options['range']['min'];
            }

            if (!empty($options['range']['max'])) {
                $params['lte'] = $options['range']['max'];
            }

            $minMaxQuery = new \Elastica\Query\Range($options['range']['field'], $params);
            $boolQuery->addMust($minMaxQuery);
        }

        if (!empty($options['close'])) {
            $locationQuery = new \Elastica\Query\DistanceFeature($options['close']['to'], $options['close']['origin'], '100km');
            $boolQuery->addShould($locationQuery);
        }

        $result = $this->client->getIndex($collection)->search($boolQuery);

        $model = \ucfirst($collection);

        $type = "\\App\\Infrastructure\\Search\\Model\\{$model}";

        return new SearchResult(array_map(fn (array $item) => $this->serializer->deserialize($item, $type, 'json'), $result->getResults()), $result->count());
    }
}