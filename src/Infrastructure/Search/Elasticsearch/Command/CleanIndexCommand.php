<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Elasticsearch\Command;

use App\Infrastructure\Search\IndexerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CleanIndexCommand extends Command
{
    protected static $defaultName = 'app:elasticsearch:clean:index';
    protected static $defaultDescription = 'Remove collection from elasticsearch';
    private IndexerInterface $indexer;

    public function __construct(IndexerInterface $indexer)
    {
        parent::__construct();
        $this->indexer = $indexer;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Index Name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        $this->indexer->clean($name);

        $io->success("Index {$name} cleaned.");

        return Command::SUCCESS;
    }
}
