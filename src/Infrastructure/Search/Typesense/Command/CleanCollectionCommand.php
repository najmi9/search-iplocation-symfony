<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Typesense\Command;

use App\Infrastructure\Search\IndexerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CleanCollectionCommand extends Command
{
    protected static $defaultName = 'app:typesense:clean:collection';
    protected static $defaultDescription = 'Remove collection from typesense';
    private IndexerInterface $indexer;

    public function __construct(IndexerInterface $indexer)
    {
        parent::__construct();
        $this->indexer = $indexer;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Collection Name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // it will be good to fetch all available collections and let the user choose one
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        $this->indexer->clean($name);

        $io->success("Colection {$name} cleaned.");

        return Command::SUCCESS;
    }
}
