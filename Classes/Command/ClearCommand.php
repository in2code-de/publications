<?php

declare(strict_types=1);

namespace In2code\Publications\Command;

use Doctrine\DBAL\DBALException;
use In2code\Publications\Domain\Model\Author;
use In2code\Publications\Domain\Model\Publication;
use In2code\Publications\Utility\DatabaseUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ClearCommand
 */
class ClearCommand extends Command
{
    /**
     * Configure the command by defining the name, options and arguments
     */
    public function configure()
    {
        $this->setDescription('Clean all data of a given pid');
        $this->addArgument('pid', InputArgument::REQUIRED, 'page id where to delete records');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pid = (int)$input->getArgument('pid');
        $tables = [
            Publication::TABLE_NAME,
            Author::TABLE_NAME
        ];
        foreach ($tables as $table) {
            $connection = DatabaseUtility::getConnectionForTable($table);
            if ($pid > 0) {
                $connection->executeQuery('delete from ' . $table . ' where pid=' . $pid);
            } else {
                $connection->truncate($table);
            }
        }
        if ($pid === 0) {
            $connection = DatabaseUtility::getConnectionForTable('tx_publications_publication_author_mm');
            $connection->truncate('tx_publications_publication_author_mm');
        }
        $output->writeln(
            $pid > 0 ? 'Removed all records from page with UID=' . $pid : 'Truncated all publication tables'
        );
        return 0;
    }
}
