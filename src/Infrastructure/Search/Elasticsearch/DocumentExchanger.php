<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Elasticsearch;

use App\Services\EntityToModelService;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Document;
use JoliCode\Elastically\Messenger\DocumentExchangerInterface;

class DocumentExchanger implements DocumentExchangerInterface
{
    private EntityManagerInterface $em;
    private EntityToModelService $entityToModel;

    public function __construct(EntityManagerInterface $em, EntityToModelService $entityToModel)
    {
        $this->em = $em;
        $this->entityToModel = $entityToModel;
    }

    public function fetchDocument(string $className, string $id): ?Document
    {
        $words = explode('\\', $className);
        $entityName = \end($words);
        $indexName = strtolower($entityName);

        $data = $this->em->find("\\App\\Entity\\{$entityName}", $id);
        if ($data) {
             /** @var ModelInterface  $model*/
            $model = $this->entityToModel->{$indexName}($data, false);

            return new Document($id, $model);
        }

        return null;
    }
}