<?php

namespace Exadsrcise\Application\Commands;

use Exadsrcise\Application\Contracts\CommandInterface;
use Exadsrcise\Domain\ElementArray\IntegerGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ElementArray extends Command implements CommandInterface
{
    const SIZE_ARG = 'size';
    const DESCRIPTION = 'Write a PHP script to generate a random array of N integers.';
    const HELP = 'Randomly remove and discard an arbitary element from this newly generated array.'
    . 'Efficiently determine the value of the missing element.';

    /**
     * @var string $defaultName Name of command
     */
    protected static $defaultName = 'run:element-array';

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
            ->addArgument(self::SIZE_ARG, InputArgument::OPTIONAL, 'Array size', 500)
        ;
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
        $generator = new IntegerGenerator(
            intval($input->getArgument(self::SIZE_ARG))
        );

        $output->writeln("Was created: ");
        $output->writeln($generator->run());

        $output->write("The removed item was: ");
        $output->writeln($generator->getRemovedItems());

        return Command::SUCCESS;
    }
}
