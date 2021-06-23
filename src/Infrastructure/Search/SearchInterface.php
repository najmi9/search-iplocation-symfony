<?php

declare(strict_types=1);

namespace App\Infrastructure\Search;

interface SearchInterface
{
    /**
     * Search a content.
     *
     * @param string $collection collection to search in
     * @param string $q search query
     * @param array $options ex: [
     *   'range' => [
     *      'field' => 'field',
     *      'min' => $minValue,
     *      'max' => $maxValue
     *    ],
     *    'search_in' => [
     *         'field1', 'field2',
     *     ],
     * 
     *     'close' => [
     *          'to' => 'field',
     *           'origin' => $origin
     *     ]
     * ]
     * @param integer $limit
     * @param integer $page
     */
    public function search(string $collection, string $q, array $options = [], int $limit = 50, int $page = 1): SearchResult;
}
