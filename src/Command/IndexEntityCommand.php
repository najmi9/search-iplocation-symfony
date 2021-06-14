<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class IndexEntityCommand extends Command
{
    protected static $defaultName = 'index:entity';
    protected static $defaultDescription = 'Index entity';
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        parent::__construct();
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $helper = $this->getHelper('question');

        $entities = $this->em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        $question = new ChoiceQuestion('What entity do you want to index?', $entities, 0);

        $name = $helper->ask($input, $output, $question);

        $prefix = explode('\\', $name)[2];

        $eventName = "\\App\Events\\{$prefix}CreatedEvent";

        foreach ($this->em->getRepository($name)->findAll() as $row) {
            $ref = new \ReflectionClass($eventName);
            $event = $ref->newInstanceArgs([$row]);
            $this->dispatcher->dispatch($event);
        }

        $io->success("Entity {$name} indexed.");

        return Command::SUCCESS;
    }
}
