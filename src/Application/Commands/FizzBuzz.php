<?php

namespace Exadsrcise\Application\Commands;

use Exadsrcise\Application\Contracts\CommandInterface;
use Exadsrcise\Domain\FizzBuzz\NumberRangeChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FizzBuzz extends Command implements CommandInterface
{
    const MIN_ARG = 'min';
    const MAX_ARG = 'max';
    const DESCRIPTION = 'Write a PHP script that prints all integer values from min to max.';
    const HELP = 'Write "Fizz" in multiple of three and "Buzz" for the multiples of five.'
    . 'Values which are multiples of both three and five should output as "FizzBuzz".';

    /**
     * @var string $defaultName Name of command
     */
    protected static $defaultName = 'run:fizz-buzz';

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
            ->setHelp(self::HELP)
            ->addArgument(self::MIN_ARG, InputArgument::REQUIRED, 'Min Number')
            ->addArgument(self::MAX_ARG, InputArgument::REQUIRED, 'Max Number');
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
        // Instantiate number range.
        // Since input args were given as string values,
        // we should cast them into integer.
        $numberRangeChecker = new NumberRangeChecker(
            intval($input->getArgument(self::MIN_ARG)),
            intval($input->getArgument(self::MAX_ARG))
        );

        foreach ($numberRangeChecker->fetch() as $number)
            $output->writeln($number->output());

        return Command::SUCCESS;
    }
}
