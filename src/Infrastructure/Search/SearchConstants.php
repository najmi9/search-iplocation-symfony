<?php

declare(strict_types=1);

namespace App\Infrastructure\Search;

/**
 * The names of the indexes or collection should be the same as the name of the entities in lowercase
 */
class SearchConstants
{
    const PRODUCTS_INDEX = 'product';
    const USERS_INDEX = 'user';
    const STORES_INDEX = 'store';

    const ELASTICSEARCH = 0;
    const TYPESENSE = 1;
}
