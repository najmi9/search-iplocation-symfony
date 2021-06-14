<?php

namespace App\Infrastructure\Search\Item;

use App\Infrastructure\Search\SearchResultItemInterface;

class TypesenseProductItem implements SearchResultItemInterface
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

    public function category(): string
    {
        return $this->item['document']['category'];
    }

    public function price(): float
    {
        return $this->item['document']['price'];
    }

    public function description(): string
    {
        return $this->item['document']['description'];
    }

    public function shortDescription(): string
    {
        foreach($this->item['highlights'] as $highlight) {
            if ('shortDescription' === $highlight['field']) {
                return $highlight['value'];
            }
        }

        return $this->item['document']['shortDescription'];
    }
}
