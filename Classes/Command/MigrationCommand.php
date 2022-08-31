<?php

declare(strict_types=1);

namespace In2code\Publications\Command;

use In2code\Publications\Migration\MigrateFromBib;
use In2code\Publications\Utility\ObjectUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrationCommand
 */
class MigrationCommand extends Command
{
    /**
     * Configure the command by defining the name, options and arguments
     */
    public function configure()
    {
        $this->setDescription('Migrate existing bib records to new publications records');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migration = ObjectUtility::getObjectManager()->get(MigrateFromBib::class);
        $numberOfPublications = $migration->migrate();
        $output->writeln('Migrated ' . $numberOfPublications . ' publications from bib');
        return 0;
    }
}
