<?php

namespace App\Infrastructure\Search\Items;

use App\Infrastructure\Search\SearchResultItemInterface;

class TypesenseUserItem implements SearchResultItemInterface
{
    /**
     * An item store by typesense.
     *
     *  {
     *    document: {
     *      field: 'value',
     *      field2: 'value',
     *   },
     *   highlights:[
     *      {
     *          field:"title",
     *          snippet: "an excerpt with <mark>",
     *          value: "the whole string with <mark>",
     *      }
     */
    private array $item;

    public function __construct(array $item)
    {
        $this->item = $item;
    }

    public function getName(): string
    {
        foreach ($this->item['highlights'] as $highlight) {
            if ('name' === $highlight['field']) {
                return $highlight['value'];
            }
        }

        return $this->item['document']['name'];
    }

    public function getUrl(): string
    {
        return $this->item['document']['url'];
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return new \DateTimeImmutable('@'.$this->item['document']['created_at']);
    }
}
