<?php
declare(strict_types = 1);

namespace PAGEmachine\Searchable\Command\Index;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ResetCommand extends AbstractIndexCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setDescription('Reset search index')
            ->addArgument('language', InputArgument::OPTIONAL, 'Language of index to reset');
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->indexingService->resetIndex(
            $input->hasArgument('language') ? (int)$input->getArgument('language') : null
        );

        return 0;
    }
}
