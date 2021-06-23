<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Events;

use App\Infrastructure\Search\SearchConstants;
use Symfony\Component\Yaml\Yaml;

class EntityCreatedEvent
{
    private int $type;
    private string $indexName;
    private string $root_dir;
    private array $content;
    private array $options;

    /**
     * @param SearchConstant::PRODUCTS|SearchConstant::USERS $indexName
     * @param string $root_dir root dirctory
     * @param array $content content to index
     * @param SearchConstants::ELASTICSEARCH|SearchConstants::TYPESENSE $type
     * @param array $options
     */
    public function __construct(string $indexName, string $root_dir, array $content, int $type = SearchConstants::ELASTICSEARCH, array $options = [])
    {
        $this->type = $type;
        $this->indexName = $indexName;
        $this->root_dir = $root_dir;
        $this->content = $content;
        $this->options = $options;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getIndexName(): string
    {
        return $this->indexName;
    }

    public function getFields(): array
    {
        $fields = [];
        $mapping = Yaml::parse(file_get_contents("{$this->root_dir}/config/typesense/{$this->indexName}_mapping.yaml"));

        foreach ($mapping['mapping'] as $key => $value) {
            $field = ['name' => $key, 'type' => $value['type']];
            if (!empty($value['facet'])) {
                $field['facet'] = $value['facet'];
            }
            $fields[] = $field;
        }

        return $fields;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}