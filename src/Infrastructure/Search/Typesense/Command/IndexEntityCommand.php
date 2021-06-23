<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Typesense\Command;

use App\Infrastructure\Search\Events\EntityCreatedEvent;
use App\Services\EntityToModelService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class IndexEntityCommand extends Command
{
    protected static $defaultName = 'app:typesense:index:entity';
    protected static $defaultDescription = 'Index entity';
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;
    private string $root_dir;
    private EntityToModelService $entityToModel;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher, EntityToModelService $entityToModel, string $root_dir)
    {
        parent::__construct();
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->entityToModel = $entityToModel;
        $this->root_dir = $root_dir;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $helper = $this->getHelper('question');

        $entities = $this->em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        $question = new ChoiceQuestion('What entity do you want to index?', $entities, 0);

        $name = $helper->ask($input, $output, $question);

        $prefix = explode('\\', $name)[2];

        $indexName = strtolower($prefix);

        foreach ($this->em->getRepository($name)->findAll() as $row) {
            $event = new EntityCreatedEvent($indexName, $this->root_dir, $this->entityToModel->product($row));
            $this->dispatcher->dispatch($event);
        }

        $io->success("Entity {$name} indexed.");

        return Command::SUCCESS;
    }
}
