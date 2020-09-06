<?php

namespace Exadsrcise\Application\Commands;

use Exadsrcise\Application\Contracts\CommandInterface;
use Exadsrcise\Domain\Datetime\DatetimeConverter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Lottery extends Command implements CommandInterface
{
    const DESCRIPTION = 'Calculates and returns the next valid draw date based on the current '.
                        'datetime and also on an optionally supplied date and time.';
    const HELP = 'Calculates the next Tuesday and Saturday date.';

    /**
     * @var string $defaultName Name of command
     */
    protected static $defaultName = 'run:lottery';

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
        $converter = new DatetimeConverter();
        $requiredTime = (new \DateTime())->setTime(20, 0);

        $tuesday = $converter->getNextDateOfWeekDay('tuesday', $requiredTime);
        $output->writeln("Next Lottery of Tuesday will be {$tuesday->format('Y-m-d H:i:s')}");

        $saturday = $converter->getNextDateOfWeekDay('saturday', $requiredTime);
        $output->writeln("Next Lottery of Saturday will be {$saturday->format('Y-m-d H:i:s')}");

        return Command::SUCCESS;
    }
}
