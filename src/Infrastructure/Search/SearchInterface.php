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
     *    'query_by' => 'field1,field2',
     *    'highlight_full_fields' => 'field1',
     *    'filter_by' => 'num_employees:>100',
     *    'sort_by' => 'num_employees:desc',
     *    'exclude_fields' => 'field1,field2',
     * ]
     * @param integer $limit
     * @param integer $page
     */
    public function search(string $collection, string $q, array $options = [], int $limit = 50, int $page = 1): SearchResult;
}
