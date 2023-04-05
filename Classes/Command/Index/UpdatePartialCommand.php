<?php

declare(strict_types = 1);

namespace PAGEmachine\Searchable\Command\Index;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class UpdatePartialCommand extends AbstractIndexCommand
{
    /**
     * Configures the current command.
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Process search indexer updates')
            ->addArgument('type', InputArgument::OPTIONAL, 'Type to run indexers for, all by default');
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->indexingService->indexPartial($input->getArgument('type') ?: '');

        return 0;
    }
}
