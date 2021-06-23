<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Elasticsearch\Command;

use App\Services\EntityToModelService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Document;
use JoliCode\Elastically\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class IndexEntityCommand extends Command
{
    protected static $defaultName = 'app:elasticsearch:index:entity';
    private $client;
    private EntityManagerInterface $em;
    private EntityToModelService $entityToModel;

    protected function configure()
    {
        $this->setDescription('Build new index from scratch and populate.');
    }

    public function __construct(string $name = null, Client $client, EntityManagerInterface $em, EntityToModelService $entityToModel)
    {
        parent::__construct($name);
        $this->client = $client;
        $this->em = $em;
        $this->entityToModel = $entityToModel;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $entities = $this->em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        $question = new ChoiceQuestion('What entity do you want to index?', $entities, 0);

        $className = $helper->ask($input, $output, $question);

        $prefix = explode('\\', $className)[2];

        $indexName = strtolower($prefix);

        $indexBuilder = $this->client->getIndexBuilder();
        $newIndex = $indexBuilder->createIndex($indexName);
        $indexer = $this->client->getIndexer();

        /** @var ServiceEntityRepository $repo */
        $repo = $this->em->getRepository($className);
        $data = $repo->createQueryBuilder($indexName)->getQuery()->iterate();

        foreach ($data as $row) {
            $indexer->scheduleIndex($newIndex, new Document((string) $row[0]->getId(), $this->entityToModel->{$indexName}($row[0])));
        }

        $indexer->flush();

        $indexBuilder->markAsLive($newIndex, $indexName);
        $indexBuilder->speedUpRefresh($newIndex);
        $indexBuilder->purgeOldIndices($indexName);

        $io->success("Index {$indexName} created.");

        return Command::SUCCESS;
    }
}
