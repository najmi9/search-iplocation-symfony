<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Elasticsearch;

use Doctrine\ORM\EntityManagerInterface;
use Elastica\Document;
use JoliCode\Elastically\Messenger\DocumentExchangerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class DocumentExchanger implements DocumentExchangerInterface
{
    private EntityManagerInterface $em;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    public function fetchDocument(string $className, string $id): ?Document
    {
        $entityName = \end(explode('\\', $className));

        $data = $this->em->find("\\App\\Entity\\{$entityName}", $id);
        if ($data) {
             /** @var ModelInterface  $model*/
            $model = $this->serializer->deserialize($data, $className, 'json');
            $model->setId((string) $model->getId());

            return new Document($id, $model);
        }

        return null;
    }
}