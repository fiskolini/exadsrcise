<?php

namespace Exadsrcise\Application\Commands;

use Exadsrcise\Application\Contracts\CommandInterface;
use Exadsrcise\Persistency\Repositories\TestRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function implode;

class Database extends Command implements CommandInterface
{
    const DESCRIPTION = 'Connect to a MySQL (InnoDB) database and query for all records of Test entity.';
    const HELP = 'Execute a DB Query to SELECT records';

    /**
     * @var string $defaultName Name of command
     */
    protected static $defaultName = 'run:database';

    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            // the short description shown while running command
            ->setDescription(self::DESCRIPTION)

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp(self::HELP);
    }

    /**
     * Command logic execution
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Insert a new record
        TestRepository::insert('Francisco', 28, 'Software Developer');

        // Fetch all records
        TestRepository::fetchAll(function ($test) use ($output) {
            $output->writeln(implode(", ", $test));
        });

        return Command::SUCCESS;
    }
}
